CREATE TABLE gaia_edc_exchange_reports (
 thisid BIGINT NOT NULL AUTO_INCREMENT PRIMARY KEY,
 contract_id INT NOT NULL,
 exchange_id CHAR(36) NOT NULL,
 reporter_type VARCHAR(20) NOT NULL,
 reporter_did VARCHAR(255) NOT NULL,
 edc_transfer_process_id VARCHAR(128) NOT NULL,
 edc_agreement_id VARCHAR(128) NOT NULL,
 payload_hash CHAR(64) NOT NULL,
 bytes_transferred BIGINT NOT NULL,
 created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
 UNIQUE KEY exchange_report (contract_id,exchange_id,reporter_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
