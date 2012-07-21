--
-- Table structure for table `markov`
--

CREATE TABLE IF NOT EXISTS `tweet` (
  `id` integer primary key,
  `tweet` text NOT NULL
);

CREATE TABLE IF NOT EXISTS `markov` (
  `id` integer primary key,
  `lex1` text NOT NULL,
  `lex2` text NOT NULL,
  `lex3` text NOT NULL,
  `cnt` integer DEFAULT 1
);

CREATE INDEX markov_idx_1 ON markov(`lex1`);
CREATE INDEX markov_idx_2 ON markov(`lex2`);
CREATE INDEX markov_idx_3 ON markov(`lex3`);