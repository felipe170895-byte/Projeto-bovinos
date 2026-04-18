BEGIN;

CREATE TABLE IF NOT EXISTS login_attempts (
    id BIGSERIAL PRIMARY KEY,
    email VARCHAR(160) NOT NULL,
    success BOOLEAN NOT NULL DEFAULT FALSE,
    ip_origem INET,
    attempted_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGSERIAL PRIMARY KEY,
    usuario_id BIGINT NULL REFERENCES usuarios(id) ON DELETE SET NULL,
    fazenda_id BIGINT NULL REFERENCES fazendas(id) ON DELETE SET NULL,
    acao VARCHAR(80) NOT NULL,
    detalhes TEXT,
    ip_origem INET,
    user_agent VARCHAR(255),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS schema_migrations (
    version VARCHAR(50) PRIMARY KEY,
    executed_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_login_attempts_email_time ON login_attempts (email, attempted_at DESC);
CREATE INDEX IF NOT EXISTS idx_audit_logs_usuario ON audit_logs (usuario_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_fazenda ON audit_logs (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_audit_logs_created_at ON audit_logs (created_at DESC);

COMMIT;
