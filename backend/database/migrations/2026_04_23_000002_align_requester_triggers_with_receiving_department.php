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
                DECLARE requester_department_id INT;

                SELECT u.DepartmentID
                INTO requester_department_id
                FROM `user` u
                WHERE u.UserID = NEW.RequestedByUserID
                LIMIT 1;

                IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'PTR requester must belong to receiving department';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertytransfer_requester_role_check_update
            BEFORE UPDATE ON propertytransfer
            FOR EACH ROW
            BEGIN
                DECLARE requester_department_id INT;

                IF NEW.RequestedByUserID <> OLD.RequestedByUserID OR NEW.ToDepartmentID <> OLD.ToDepartmentID THEN
                    SELECT u.DepartmentID
                    INTO requester_department_id
                    FROM `user` u
                    WHERE u.UserID = NEW.RequestedByUserID
                    LIMIT 1;

                    IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'PTR requester must belong to receiving department';
                    END IF;
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertyborrow_requester_role_check
            BEFORE INSERT ON propertyborrow
            FOR EACH ROW
            BEGIN
                DECLARE requester_department_id INT;

                SELECT u.DepartmentID
                INTO requester_department_id
                FROM `user` u
                WHERE u.UserID = NEW.RequestedByUserID
                LIMIT 1;

                IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                    SIGNAL SQLSTATE '45000'
                    SET MESSAGE_TEXT = 'PBR requester must belong to borrowing department';
                END IF;
            END
        SQL);

        DB::unprepared(<<<'SQL'
            CREATE TRIGGER trg_propertyborrow_requester_role_check_update
            BEFORE UPDATE ON propertyborrow
            FOR EACH ROW
            BEGIN
                DECLARE requester_department_id INT;

                IF NEW.RequestedByUserID <> OLD.RequestedByUserID OR NEW.ToDepartmentID <> OLD.ToDepartmentID THEN
                    SELECT u.DepartmentID
                    INTO requester_department_id
                    FROM `user` u
                    WHERE u.UserID = NEW.RequestedByUserID
                    LIMIT 1;

                    IF requester_department_id IS NULL OR requester_department_id <> NEW.ToDepartmentID THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'PBR requester must belong to borrowing department';
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
