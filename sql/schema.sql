BEGIN;

CREATE TABLE IF NOT EXISTS fazendas (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    responsavel VARCHAR(120),
    cidade VARCHAR(100),
    uf CHAR(2),
    telefone VARCHAR(20),
    observacoes TEXT,
    ativa BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_fazendas_uf CHECK (uf IS NULL OR uf ~ '^[A-Z]{2}$')
);

CREATE TABLE IF NOT EXISTS usuarios (
    id BIGSERIAL PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    email VARCHAR(160) NOT NULL UNIQUE,
    senha_hash VARCHAR(255) NOT NULL,
    perfil VARCHAR(20) NOT NULL DEFAULT 'funcionario',
    fazenda_id BIGINT NULL REFERENCES fazendas(id) ON DELETE SET NULL,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_usuarios_perfil CHECK (perfil IN ('admin', 'funcionario'))
);

CREATE TABLE IF NOT EXISTS usuarios_fazendas (
    usuario_id BIGINT NOT NULL REFERENCES usuarios(id) ON DELETE CASCADE,
    fazenda_id BIGINT NOT NULL REFERENCES fazendas(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    PRIMARY KEY (usuario_id, fazenda_id)
);

CREATE TABLE IF NOT EXISTS lotes (
    id BIGSERIAL PRIMARY KEY,
    fazenda_id BIGINT NOT NULL REFERENCES fazendas(id) ON DELETE CASCADE,
    nome VARCHAR(100) NOT NULL,
    etapa VARCHAR(80),
    retiro VARCHAR(100),
    descricao TEXT,
    ativo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT uq_lotes_nome_por_fazenda UNIQUE (fazenda_id, nome)
);

CREATE TABLE IF NOT EXISTS animais (
    id BIGSERIAL PRIMARY KEY,
    fazenda_id BIGINT NOT NULL REFERENCES fazendas(id) ON DELETE CASCADE,
    numero_brinco VARCHAR(50) NOT NULL,
    raca VARCHAR(80),
    categoria_atual VARCHAR(80),
    status_reprodutivo VARCHAR(80),
    lote_id BIGINT NULL REFERENCES lotes(id) ON DELETE SET NULL,
    etapa_atual VARCHAR(80),
    retiro_atual VARCHAR(80),
    data_nasc DATE,
    sexo CHAR(1),
    mae_id BIGINT NULL REFERENCES animais(id) ON DELETE SET NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_animais_sexo CHECK (sexo IS NULL OR sexo IN ('M', 'F')),
    CONSTRAINT uq_animais_brinco_por_fazenda UNIQUE (fazenda_id, numero_brinco)
);

CREATE TABLE IF NOT EXISTS animais_lotes (
    id BIGSERIAL PRIMARY KEY,
    animal_id BIGINT NOT NULL REFERENCES animais(id) ON DELETE CASCADE,
    lote_id BIGINT NOT NULL REFERENCES lotes(id) ON DELETE CASCADE,
    data_inicio DATE NOT NULL,
    data_fim DATE,
    motivo VARCHAR(150),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_animais_lotes_datas CHECK (data_fim IS NULL OR data_fim >= data_inicio)
);

CREATE TABLE IF NOT EXISTS eventos_reprodutivos (
    id BIGSERIAL PRIMARY KEY,
    animal_id BIGINT NOT NULL REFERENCES animais(id) ON DELETE CASCADE,
    tipo_evento VARCHAR(40) NOT NULL,
    data_evento DATE NOT NULL,
    etapa VARCHAR(80),
    lote_id BIGINT NULL REFERENCES lotes(id) ON DELETE SET NULL,
    resultado VARCHAR(120),
    detalhes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_eventos_tipo CHECK (tipo_evento IN ('cio', 'inseminacao', 'diagnostico_gestacao', 'parto', 'secagem', 'outro'))
);


CREATE TABLE IF NOT EXISTS bezerros (
    id BIGSERIAL PRIMARY KEY,
    fazenda_id BIGINT NOT NULL REFERENCES fazendas(id) ON DELETE CASCADE,
    animal_mae_id BIGINT NULL REFERENCES animais(id) ON DELETE SET NULL,
    numero_brinco VARCHAR(50) NOT NULL,
    sexo CHAR(1),
    data_nasc DATE NOT NULL,
    peso_nasc_kg NUMERIC(6,2),
    observacoes TEXT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT chk_bezerros_sexo CHECK (sexo IS NULL OR sexo IN ('M', 'F')),
    CONSTRAINT uq_bezerros_brinco_por_fazenda UNIQUE (fazenda_id, numero_brinco)
);

CREATE INDEX IF NOT EXISTS idx_usuarios_fazenda_padrao ON usuarios (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_usuarios_fazendas_fazenda ON usuarios_fazendas (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_lotes_fazenda ON lotes (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_animais_fazenda ON animais (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_animais_lote ON animais (lote_id);
CREATE INDEX IF NOT EXISTS idx_animais_mae ON animais (mae_id);
CREATE INDEX IF NOT EXISTS idx_animais_lotes_animal ON animais_lotes (animal_id);
CREATE INDEX IF NOT EXISTS idx_animais_lotes_lote ON animais_lotes (lote_id);
CREATE INDEX IF NOT EXISTS idx_eventos_animal ON eventos_reprodutivos (animal_id);
CREATE INDEX IF NOT EXISTS idx_eventos_data ON eventos_reprodutivos (data_evento);
CREATE INDEX IF NOT EXISTS idx_eventos_lote ON eventos_reprodutivos (lote_id);


CREATE INDEX IF NOT EXISTS idx_bezerros_fazenda ON bezerros (fazenda_id);
CREATE INDEX IF NOT EXISTS idx_bezerros_mae ON bezerros (animal_mae_id);

COMMIT;
