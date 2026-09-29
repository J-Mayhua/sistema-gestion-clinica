ALTER TABLE registrar_citas
    ADD COLUMN disponibilidad_id INT(11) DEFAULT NULL AFTER doctor_id,
    ADD UNIQUE KEY uq_registrar_citas_disponibilidad (disponibilidad_id),
    ADD CONSTRAINT fk_registrar_citas_disponibilidad
        FOREIGN KEY (disponibilidad_id)
        REFERENCES tabla_disponibilidad (disponibilidad_id);

-- Rollback
-- ALTER TABLE registrar_citas
--     DROP FOREIGN KEY fk_registrar_citas_disponibilidad,
--     DROP INDEX uq_registrar_citas_disponibilidad,
--     DROP COLUMN disponibilidad_id;
