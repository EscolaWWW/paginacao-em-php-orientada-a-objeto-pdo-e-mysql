
CREATE TABLE `funcionario` (
  `idFuncionario` int(11) NOT NULL,
  `nome` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `senha` varchar(45) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `funcionario` (`idFuncionario`, `nome`, `email`, `senha`, `data_cadastro`) VALUES
(1, 'Contato', 'contato@escolawww.com.br', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '2017-10-09 10:30:43');
