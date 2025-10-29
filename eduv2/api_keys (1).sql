

CREATE TABLE `api_keys` (
  `id` int(11) NOT NULL,
  `api_key` varchar(255) NOT NULL,
  `usos_actuales` int(11) DEFAULT 0,
  `timestamp_reset` bigint(20) DEFAULT 0,
  `usos` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

