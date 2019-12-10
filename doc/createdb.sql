CREATE TABLE consent (
    visitor_id TEXT,
    settings TEXT,
    last_change INTEGER,
    CONSTRAINT pk_consent PRIMARY KEY (visitor_id)
);