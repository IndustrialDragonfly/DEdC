USE dedc;

#Insert a user
INSERT INTO users (id, userName, organization) VALUES ('RlyxJ3ZxPsdOr9rFTb9UrTrGKRBUQxWbclf9Gv0Fz1Mx', 'Geoff', 'InD');
INSERT INTO users (id, userName, organization) VALUES ('3LToOJ5L0gRLVRcYDThHYxne49Ai13xKHwVJx6Y4Ixox', 'Jeb', 'InD');
INSERT INTO users (id, userName, organization) VALUES ('4Q8RTb7YK93Zt7QhJx2LZlFPl8igZkYmauAhIh2djMAx', 'Eve', 'Evil Inc');

#password for Geoff is password1
INSERT INTO hash (id, hash) VALUES ('RlyxJ3ZxPsdOr9rFTb9UrTrGKRBUQxWbclf9Gv0Fz1Mx', '$2y$10$RMwCqfIrwxTmnzQoEw8B4ePQcH3TmVIQ35xQcTbltOdtXbPvT.3pi');

#password for Bob is password2
INSERT INTO hash (id, hash) VALUES ('3LToOJ5L0gRLVRcYDThHYxne49Ai13xKHwVJx6Y4Ixox', '$2y$10$VVK5rcQAz/wd59ckep16Quu8T/vJ7ExXRmW6Ya3lVdc2xnwQV6xUS');

#password for Eve is password3
INSERT INTO hash (id, hash) VALUES ('4Q8RTb7YK93Zt7QhJx2LZlFPl8igZkYmauAhIh2djMAx', '$2y$10$o5iTjkxknv6rcQXEX/7ZhOvG4abRfaQ488vxpavF1rXdIt0Hd/qrC');
