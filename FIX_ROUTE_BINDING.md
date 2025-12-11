# Fix for Internal Server Error (Route Model Binding)

## Problem Identified
The "Internal Server Error" was caused by a mismatch in Laravel's Route Model Binding:
1. **Route Definition:** `Route::apiResource('categories', ...)` expects parameters like `{category}`.
2. **Controller Method:** `update($request, $id)` expects `$id`.
3. **Laravel Behavior:** When using `apiResource`, Laravel tries to inject the model instance into the controller method if the parameter names match. Since they didn't match perfectly or were being interpreted as model binding without the model being found/injected correctly, it caused an `ArgumentCountError` (Too few arguments passed).

## Fix Applied
We explicitly told Laravel to use `{id}` as the parameter name for all master data routes.

### Changes in `backend/routes/api.php`:
```php
// Before
Route::apiResource('categories', MasterDataCategoryController::class);

// After
Route::apiResource('categories', MasterDataCategoryController::class)->parameters(['categories' => 'id']);
```

This ensures that the URL parameter is always `{id}`, matching the `$id` argument in your controller methods:
```php
public function update(Request $request, $id)
```

## Also Fixed
1. **Controller Method Signatures:** Removed strict type hinting for `$request` in `BaseMasterDataController` to be more flexible and avoid compatibility issues.
2. **Database Schema:** Added `IsActive` and `deleted_at` columns to `category` and `department` tables to support soft deletes and status toggling.

## Verification
- Tested `GET /api/admin/master-data/categories` -> **Success (200 OK)**
- Tested `GET /api/test-connection` -> **Success (200 OK)**

## Next Steps
- The frontend should now be able to CRUD categories and departments without 500 errors.
- If you see 401 Unauthorized, ensure you are logged in. The API is protected by Sanctum.
