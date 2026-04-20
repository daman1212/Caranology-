-- ================================================
--  CARANOLOGY — Database Setup
--  HOW TO RUN:
--  1. Open Terminal on your Mac
--  2. Type: ssh dtw348@titan.cs.uregina.ca
--  3. Then type: mysql -u dtw348 -p dtw348
--  4. Paste everything below and press Enter
-- ================================================

CREATE TABLE IF NOT EXISTS users (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(100)  NOT NULL,
    email      VARCHAR(150)  NOT NULL UNIQUE,
    username   VARCHAR(80)   NOT NULL UNIQUE,
    dob        DATE          NOT NULL,
    password   VARCHAR(255)  NOT NULL,
    avatar     VARCHAR(255)  DEFAULT NULL,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS questions (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT           NOT NULL,
    title      VARCHAR(255)  NOT NULL,
    body       TEXT          NOT NULL,
    upvotes    INT           DEFAULT 0,
    downvotes  INT           DEFAULT 0,
    created_at TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS answers (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT  NOT NULL,
    user_id     INT  NOT NULL,
    body        TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS votes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    question_id INT NOT NULL,
    vote_type   ENUM('up','down') NOT NULL,
    UNIQUE KEY one_vote_per_user (user_id, question_id),
    FOREIGN KEY (user_id)     REFERENCES users(id)     ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);
