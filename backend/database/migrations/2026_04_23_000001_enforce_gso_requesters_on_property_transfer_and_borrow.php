<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertytransfer_requester_role_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertytransfer_requester_role_check_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertyborrow_requester_role_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertyborrow_requester_role_check_update');

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertytransfer_requester_role_check
            BEFORE INSERT ON propertytransfer
            FOR EACH ROW
            BEGIN
                DECLARE role_name VARCHAR(50);

                SELECT ur.RoleName
                INTO role_name
                FROM `user` u
                LEFT JOIN userrole ur ON ur.UserRoleID = u.UserRoleID
                WHERE u.UserID = NEW.RequestedByUserID
                LIMIT 1;

                IF role_name IS NULL OR role_name NOT IN ('Admin', 'GSO Staff') THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Only Admin or GSO Staff can create PTR requests';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertytransfer_requester_role_check_update
            BEFORE UPDATE ON propertytransfer
            FOR EACH ROW
            BEGIN
                DECLARE role_name VARCHAR(50);

                IF NEW.RequestedByUserID <> OLD.RequestedByUserID THEN
                    SELECT ur.RoleName
                    INTO role_name
                    FROM `user` u
                    LEFT JOIN userrole ur ON ur.UserRoleID = u.UserRoleID
                    WHERE u.UserID = NEW.RequestedByUserID
                    LIMIT 1;

                    IF role_name IS NULL OR role_name NOT IN ('Admin', 'GSO Staff') THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Only Admin or GSO Staff can be PTR requester';
                    END IF;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertyborrow_requester_role_check
            BEFORE INSERT ON propertyborrow
            FOR EACH ROW
            BEGIN
                DECLARE role_name VARCHAR(50);

                SELECT ur.RoleName
                INTO role_name
                FROM `user` u
                LEFT JOIN userrole ur ON ur.UserRoleID = u.UserRoleID
                WHERE u.UserID = NEW.RequestedByUserID
                LIMIT 1;

                IF role_name IS NULL OR role_name NOT IN ('Admin', 'GSO Staff') THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'Only Admin or GSO Staff can create PBR requests';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertyborrow_requester_role_check_update
            BEFORE UPDATE ON propertyborrow
            FOR EACH ROW
            BEGIN
                DECLARE role_name VARCHAR(50);

                IF NEW.RequestedByUserID <> OLD.RequestedByUserID THEN
                    SELECT ur.RoleName
                    INTO role_name
                    FROM `user` u
                    LEFT JOIN userrole ur ON ur.UserRoleID = u.UserRoleID
                    WHERE u.UserID = NEW.RequestedByUserID
                    LIMIT 1;

                    IF role_name IS NULL OR role_name NOT IN ('Admin', 'GSO Staff') THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Only Admin or GSO Staff can be PBR requester';
                    END IF;
                END IF;
            END
        SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertytransfer_requester_role_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertytransfer_requester_role_check_update');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertyborrow_requester_role_check');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_propertyborrow_requester_role_check_update');
    }
};
