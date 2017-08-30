DROP TABLE IF EXISTS triggered_contacts;

CREATE TABLE triggered_contacts (
  id SERIAL,
  contact_id INT,
  customer_id INT,
  resource_id VARCHAR(255),
  trigger_id VARCHAR(255),
  program_id INT,
  node_id INT,
  time TIMESTAMP
);