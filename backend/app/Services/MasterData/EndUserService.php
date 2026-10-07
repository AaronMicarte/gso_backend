<?php

namespace App\Services\MasterData;

use App\Models\MasterData\EndUser;

class EndUserService extends BaseMasterDataService
{
    protected $searchableFields = ['FullName', 'Email', 'ContactNumber'];
    protected $defaultSortField = 'FullName';
    protected $defaultSortOrder = 'asc';

    public function __construct()
    {
        $this->model = EndUser::class;
    }

    protected function applyFilters($query, array $params): void
    {
        if (array_key_exists('is_active', $params) && $params['is_active'] !== null && $params['is_active'] !== '') {
            $isActive = filter_var($params['is_active'], FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('IsActive', $isActive);
            }
        }

        if (!empty($params['department_id'])) {
            $query->where('DepartmentID', $params['department_id']);
        }

        if (!empty($params['position_id'])) {
            $query->where('PositionID', $params['position_id']);
        }
    }

    public function update($id, array $data): ?\Illuminate\Database\Eloquent\Model
    {
        return DB::transaction(function () use ($id, $data) {
            $endUser = $this->findById($id);
            if (!$endUser) {
                return null;
            }

            $oldName = trim((string) ($endUser->FullName ?? ''));
            $endUser->update($data);
            $endUser = $endUser->fresh();
            $newName = trim((string) ($endUser->FullName ?? ''));

            if ($newName !== '' && strtolower($newName) !== strtolower($oldName)) {
                // Update propertyissuance.IssuedToName
                DB::table('propertyissuance')
                    ->where('IssuedTo', (int) $endUser->EndUserID)
                    ->update(['IssuedToName' => $newName]);

                // Update property_issuance_assignments
                if ($oldName !== '') {
                    DB::table('property_issuance_assignments')
                        ->whereRaw('LOWER(TRIM(receiver_name)) = ?', [strtolower($oldName)])
                        ->update(['receiver_name' => $newName]);
                }

                // Sync User table if matching Department Head or Email
                if (!empty($endUser->Email)) {
                    DB::table('user')
                        ->whereNotNull('Email')
                        ->whereRaw('LOWER(Email) = ?', [strtolower(trim((string) $endUser->Email))])
                        ->update(['FullName' => $newName, 'UpdatedDate' => now()]);
                }
                if ($endUser->DepartmentID) {
                    DB::table('user')
                        ->where('DepartmentID', (int) $endUser->DepartmentID)
                        ->where('UserRoleID', 4)
                        ->where(function ($q) use ($oldName) {
                            $q->whereRaw('LOWER(TRIM(FullName)) = ?', [strtolower($oldName)])
                              ->orWhereNull('FullName')
                              ->orWhere('FullName', '');
                        })
                        ->update(['FullName' => $newName, 'UpdatedDate' => now()]);
                }
            }

            return $endUser;
        });
    }
}
