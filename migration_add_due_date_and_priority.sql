
USE todolist;

ALTER TABLE todos
    ADD COLUMN due_date DATE NULL AFTER status,
    ADD COLUMN priority ENUM('low', 'medium', 'high') NOT NULL DEFAULT 'medium' AFTER due_date;
