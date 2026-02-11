<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260211000100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Initial schema for vinification backend';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE bodegas (id SERIAL NOT NULL, codigo VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, pais VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BODEGAS_CODIGO ON bodegas (codigo)');

        $this->addSql('CREATE TABLE usuarios (id SERIAL NOT NULL, bodega_id INT NOT NULL, email VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, activo BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_USUARIOS_EMAIL ON usuarios (email)');
        $this->addSql('CREATE INDEX IDX_USUARIOS_BODEGA ON usuarios (bodega_id)');

        $this->addSql('CREATE TABLE etapas (id SERIAL NOT NULL, bodega_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, orden INT NOT NULL, descripcion TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ETAPAS_BODEGA ON etapas (bodega_id)');

        $this->addSql('CREATE TABLE ubicaciones (id SERIAL NOT NULL, bodega_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, tipo VARCHAR(255) NOT NULL, capacidad_litros NUMERIC(12, 2) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_UBICACIONES_BODEGA ON ubicaciones (bodega_id)');

        $this->addSql('CREATE TABLE lotes (id SERIAL NOT NULL, bodega_id INT NOT NULL, etapa_id INT NOT NULL, ubicacion_id INT DEFAULT NULL, codigo VARCHAR(255) NOT NULL, volumen_litros NUMERIC(12, 2) NOT NULL, variedad VARCHAR(255) NOT NULL, cosecha_year INT NOT NULL, estado VARCHAR(255) NOT NULL, fecha_inicio DATE DEFAULT NULL, fecha_embotellado DATE DEFAULT NULL, regulacion JSON DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_LOTES_BODEGA ON lotes (bodega_id)');
        $this->addSql('CREATE INDEX IDX_LOTES_ETAPA ON lotes (etapa_id)');
        $this->addSql('CREATE INDEX IDX_LOTES_UBICACION ON lotes (ubicacion_id)');

        $this->addSql('CREATE TABLE mediciones (id SERIAL NOT NULL, lote_id INT NOT NULL, usuario_id INT NOT NULL, fecha_hora TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, densidad NUMERIC(8, 4) NOT NULL, temperatura_c NUMERIC(6, 2) NOT NULL, brix NUMERIC(6, 2) DEFAULT NULL, comentario TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_MEDICIONES_LOTE ON mediciones (lote_id)');
        $this->addSql('CREATE INDEX IDX_MEDICIONES_USUARIO ON mediciones (usuario_id)');

        $this->addSql('CREATE TABLE analysis_types (id SERIAL NOT NULL, codigo VARCHAR(255) NOT NULL, nombre VARCHAR(255) NOT NULL, unidad VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_ANALYSIS_TYPES_CODIGO ON analysis_types (codigo)');

        $this->addSql('CREATE TABLE analisis (id SERIAL NOT NULL, lote_id INT NOT NULL, tipo_id INT NOT NULL, unidad VARCHAR(255) NOT NULL, valor NUMERIC(10, 4) NOT NULL, metodo VARCHAR(255) DEFAULT NULL, laboratorio VARCHAR(255) DEFAULT NULL, fecha_muestra DATE NOT NULL, fecha_resultado DATE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ANALISIS_LOTE ON analisis (lote_id)');
        $this->addSql('CREATE INDEX IDX_ANALISIS_TIPO ON analisis (tipo_id)');

        $this->addSql('CREATE TABLE organolepticas (id SERIAL NOT NULL, lote_id INT NOT NULL, usuario_id INT NOT NULL, fecha DATE NOT NULL, nariz JSON DEFAULT NULL, boca JSON DEFAULT NULL, color JSON DEFAULT NULL, defectos JSON DEFAULT NULL, intensidad VARCHAR(255) DEFAULT NULL, notas_libres TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ORG_LOTE ON organolepticas (lote_id)');
        $this->addSql('CREATE INDEX IDX_ORG_USUARIO ON organolepticas (usuario_id)');

        $this->addSql('CREATE TABLE productos (id SERIAL NOT NULL, bodega_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, categoria VARCHAR(255) NOT NULL, descripcion TEXT DEFAULT NULL, unidad VARCHAR(255) NOT NULL, rango_dosis_min NUMERIC(10, 2) DEFAULT NULL, rango_dosis_max NUMERIC(10, 2) DEFAULT NULL, notas TEXT DEFAULT NULL, activo BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_PRODUCTOS_BODEGA ON productos (bodega_id)');

        $this->addSql('CREATE TABLE product_stage_compat (id SERIAL NOT NULL, producto_id INT NOT NULL, etapa_id INT NOT NULL, compatibilidad VARCHAR(255) DEFAULT NULL, dosis_recomendada NUMERIC(10, 2) DEFAULT NULL, restricciones TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_COMPAT_PRODUCTO ON product_stage_compat (producto_id)');
        $this->addSql('CREATE INDEX IDX_COMPAT_ETAPA ON product_stage_compat (etapa_id)');

        $this->addSql('CREATE TABLE acciones (id SERIAL NOT NULL, lote_id INT NOT NULL, producto_id INT NOT NULL, operador_id INT NOT NULL, etapa_id INT NOT NULL, fecha DATE NOT NULL, dosis NUMERIC(10, 2) NOT NULL, unidad VARCHAR(255) NOT NULL, objetivo TEXT DEFAULT NULL, observaciones TEXT DEFAULT NULL, estado VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ACCIONES_LOTE ON acciones (lote_id)');
        $this->addSql('CREATE INDEX IDX_ACCIONES_PRODUCTO ON acciones (producto_id)');
        $this->addSql('CREATE INDEX IDX_ACCIONES_OPERADOR ON acciones (operador_id)');
        $this->addSql('CREATE INDEX IDX_ACCIONES_ETAPA ON acciones (etapa_id)');

        $this->addSql('CREATE TABLE alert_rules (id SERIAL NOT NULL, bodega_id INT NOT NULL, etapa_id INT NOT NULL, nombre VARCHAR(255) NOT NULL, parametro VARCHAR(255) NOT NULL, operador VARCHAR(255) NOT NULL, valor NUMERIC(10, 4) DEFAULT NULL, valor_max NUMERIC(10, 4) DEFAULT NULL, periodo_dias INT DEFAULT NULL, severidad VARCHAR(255) NOT NULL, activa BOOLEAN NOT NULL, descripcion TEXT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ALERT_RULE_BODEGA ON alert_rules (bodega_id)');
        $this->addSql('CREATE INDEX IDX_ALERT_RULE_ETAPA ON alert_rules (etapa_id)');

        $this->addSql('CREATE TABLE alertas (id SERIAL NOT NULL, lote_id INT NOT NULL, regla_id INT NOT NULL, severidad VARCHAR(255) NOT NULL, estado VARCHAR(255) NOT NULL, mensaje TEXT NOT NULL, valor_detectado NUMERIC(10, 4) DEFAULT NULL, tendencia NUMERIC(10, 4) DEFAULT NULL, detected_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, ack_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, resolved_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_ALERTAS_LOTE ON alertas (lote_id)');
        $this->addSql('CREATE INDEX IDX_ALERTAS_REGLA ON alertas (regla_id)');

        $this->addSql('CREATE TABLE recommendation_rules (id SERIAL NOT NULL, bodega_id INT NOT NULL, etapa_id INT NOT NULL, producto_id INT DEFAULT NULL, nombre VARCHAR(255) NOT NULL, condiciones JSON NOT NULL, accion_sugerida TEXT NOT NULL, dosis_sugerida NUMERIC(10, 2) DEFAULT NULL, unidad VARCHAR(255) DEFAULT NULL, explicacion TEXT DEFAULT NULL, activa BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_REC_RULE_BODEGA ON recommendation_rules (bodega_id)');
        $this->addSql('CREATE INDEX IDX_REC_RULE_ETAPA ON recommendation_rules (etapa_id)');
        $this->addSql('CREATE INDEX IDX_REC_RULE_PRODUCTO ON recommendation_rules (producto_id)');

        $this->addSql('CREATE TABLE recomendaciones (id SERIAL NOT NULL, lote_id INT NOT NULL, etapa_id INT NOT NULL, producto_id INT DEFAULT NULL, entradas JSON NOT NULL, accion_sugerida TEXT NOT NULL, dosis_sugerida NUMERIC(10, 2) DEFAULT NULL, unidad VARCHAR(255) DEFAULT NULL, explicacion TEXT NOT NULL, confidence NUMERIC(5, 2) DEFAULT NULL, estado VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_REC_LOTE ON recomendaciones (lote_id)');
        $this->addSql('CREATE INDEX IDX_REC_ETAPA ON recomendaciones (etapa_id)');
        $this->addSql('CREATE INDEX IDX_REC_PRODUCTO ON recomendaciones (producto_id)');

        $this->addSql('CREATE TABLE audit_logs (id SERIAL NOT NULL, entity VARCHAR(255) NOT NULL, entity_id VARCHAR(255) NOT NULL, user_id INT DEFAULT NULL, action VARCHAR(255) NOT NULL, before JSON DEFAULT NULL, after JSON DEFAULT NULL, ip VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');

        $this->addSql('ALTER TABLE usuarios ADD CONSTRAINT FK_USUARIOS_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE etapas ADD CONSTRAINT FK_ETAPAS_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ubicaciones ADD CONSTRAINT FK_UBICACIONES_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lotes ADD CONSTRAINT FK_LOTES_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lotes ADD CONSTRAINT FK_LOTES_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE lotes ADD CONSTRAINT FK_LOTES_UBICACION FOREIGN KEY (ubicacion_id) REFERENCES ubicaciones (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mediciones ADD CONSTRAINT FK_MEDICIONES_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mediciones ADD CONSTRAINT FK_MEDICIONES_USUARIO FOREIGN KEY (usuario_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analisis ADD CONSTRAINT FK_ANALISIS_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE analisis ADD CONSTRAINT FK_ANALISIS_TIPO FOREIGN KEY (tipo_id) REFERENCES analysis_types (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organolepticas ADD CONSTRAINT FK_ORG_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE organolepticas ADD CONSTRAINT FK_ORG_USUARIO FOREIGN KEY (usuario_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE productos ADD CONSTRAINT FK_PRODUCTOS_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_stage_compat ADD CONSTRAINT FK_COMPAT_PRODUCTO FOREIGN KEY (producto_id) REFERENCES productos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE product_stage_compat ADD CONSTRAINT FK_COMPAT_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acciones ADD CONSTRAINT FK_ACCIONES_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acciones ADD CONSTRAINT FK_ACCIONES_PRODUCTO FOREIGN KEY (producto_id) REFERENCES productos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acciones ADD CONSTRAINT FK_ACCIONES_OPERADOR FOREIGN KEY (operador_id) REFERENCES usuarios (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE acciones ADD CONSTRAINT FK_ACCIONES_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alert_rules ADD CONSTRAINT FK_ALERT_RULE_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alert_rules ADD CONSTRAINT FK_ALERT_RULE_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alertas ADD CONSTRAINT FK_ALERTAS_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alertas ADD CONSTRAINT FK_ALERTAS_REGLA FOREIGN KEY (regla_id) REFERENCES alert_rules (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recommendation_rules ADD CONSTRAINT FK_REC_RULE_BODEGA FOREIGN KEY (bodega_id) REFERENCES bodegas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recommendation_rules ADD CONSTRAINT FK_REC_RULE_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recommendation_rules ADD CONSTRAINT FK_REC_RULE_PRODUCTO FOREIGN KEY (producto_id) REFERENCES productos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recomendaciones ADD CONSTRAINT FK_REC_LOTE FOREIGN KEY (lote_id) REFERENCES lotes (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recomendaciones ADD CONSTRAINT FK_REC_ETAPA FOREIGN KEY (etapa_id) REFERENCES etapas (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE recomendaciones ADD CONSTRAINT FK_REC_PRODUCTO FOREIGN KEY (producto_id) REFERENCES productos (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS audit_logs');
        $this->addSql('DROP TABLE IF EXISTS recomendaciones');
        $this->addSql('DROP TABLE IF EXISTS recommendation_rules');
        $this->addSql('DROP TABLE IF EXISTS alertas');
        $this->addSql('DROP TABLE IF EXISTS alert_rules');
        $this->addSql('DROP TABLE IF EXISTS acciones');
        $this->addSql('DROP TABLE IF EXISTS product_stage_compat');
        $this->addSql('DROP TABLE IF EXISTS productos');
        $this->addSql('DROP TABLE IF EXISTS organolepticas');
        $this->addSql('DROP TABLE IF EXISTS analisis');
        $this->addSql('DROP TABLE IF EXISTS analysis_types');
        $this->addSql('DROP TABLE IF EXISTS mediciones');
        $this->addSql('DROP TABLE IF EXISTS lotes');
        $this->addSql('DROP TABLE IF EXISTS ubicaciones');
        $this->addSql('DROP TABLE IF EXISTS etapas');
        $this->addSql('DROP TABLE IF EXISTS usuarios');
        $this->addSql('DROP TABLE IF EXISTS bodegas');
    }
}
