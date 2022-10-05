USE caern_adianti;

-- tabela de ferramentas 

CREATE TABLE `ferramentas` (
  `id` int(11) NOT NULL,
  `nome` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantidade` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- stauts ferente ao emprestimo de ferramentas

CREATE TABLE `status_ferramentas`(
    `id` int NOT NULL, 
    `nome` text COLLATE utf8mb4_unicode_ci NOT NULL,
    PRIMARY KEY (`id`)
);

-- inserindo os status predefinidos 

INSERT INTO `status_ferramentas` (`id`, `nome`) VALUES
(1, 'Emprestimo pendente'),
(2, 'Emprestimo efetuado'),
(3, 'Emprestimo devolvido'),
(4, 'Emprestimo não devolvido');

-- tabela referente ao emprestimo de ferramentas
CREATE TABLE `emprestimo`(
  `id` int NOT NULL,
  `id_usuario` int NOT NULL, 
  `id_admin` int NULL,
  `id_status` int NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_usuario`) REFERENCES system_user(`id`),
  FOREIGN KEY (`id_status`) REFERENCES status_ferramentas(`id`)
);
-- Tabela pivot da ferramenta e emprestimo
CREATE TABLE `pivot_emprestimo_ferramentas` (
    `id` int(11) NOT NULL,
    `id_emprestimo` int NOT NULL,
    `id_ferramenta` int NOT NULL,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id_emprestimo`) REFERENCES emprestimo(`id`),
  FOREIGN KEY (`id_ferramenta`) REFERENCES ferramentas(`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;