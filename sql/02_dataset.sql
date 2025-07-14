-- Inserimento Utenti (Cliente e Venditore)
INSERT INTO
    `users` (
        `name`,
        `surname`,
        `email`,
        `password_hash`,
        `telephone_number`
    )
VALUES (
        'Andrea',
        'Cecchini',
        'test@unibo.it',
        '$2y$10$0jhtQR/HqnCQsyetXpHBjeLn08FEqWGDQwCb74PToNbQSLM9Sm..a', -- testtest1
        '3331112233'
    ),
    (
        'Negozio',
        'UniBought',
        'vendor@unibo.it',
        '$2y$10$0jhtQR/HqnCQsyetXpHBjeLn08FEqWGDQwCb74PToNbQSLM9Sm..a', -- testtest1
        '021234567'
    );

-- Inserimento Ruoli
INSERT INTO `clients` (`user_id`) VALUES (1);

INSERT INTO `vendors` (`user_id`) VALUES (2);

-- Inserimento Categorie
INSERT INTO
    `categories` (`name`)
VALUES ('Cancelleria'),
    ('Libri'),
    ('Gadget'),
    ('Elettronica');

INSERT INTO
    `products` (
        `id`,
        `vendor_id`,
        `name`,
        `description`,
        `price`,
        `image_name`,
        `image_alt`,
        `quantity`
    )
VALUES (
        '550e8400-e29b-41d4-a716-446655440000',
        2,
        'Santino per esami',
        'Il santino perfetto per superare qualsiasi esame universitario. Benedetto dai professori piu esigenti, questo santino porta fortuna e saggezza durante le sessioni d\'esame. Include una preghiera speciale per la sessione di esami e un portafortuna.',
        99.99,
        'santino-esame.png',
        'Santino per esami - Il santino perfetto per superare qualsiasi esame universitario. Benedetto dai professori piu esigenti, questo santino porta fortuna e saggezza durante le sessioni d\'esame. Include una preghiera speciale per la sessione di esami e un portafortuna.',
        50
    ),
    (
        '450e8400-e29b-41d4-a716-506655440002',
        2,
        'Calcolatrice scientifica',
        'Calcolatrice scientifica professionale con 417 funzioni. Display LCD a 2 linee e 12 cifre. Ideale per calcoli statistici, trigonometrici e algebrici. Batteria inclusa e garanzia di 3 anni.',
        19.99,
        'calcolatrice-scientifica.png',
        'Calcolatrice scientifica - Calcolatrice scientifica professionale con 417 funzioni. Display LCD a 2 linee e 12 cifre. Ideale per calcoli statistici, trigonometrici e algebrici. Batteria inclusa e garanzia di 3 anni.',
        100
    ),
    (
        '750e8400-e29b-41d4-a716-506655440003',
        2,
        'Introduzione Algoritmi',
        'Il libro di riferimento per lo studio degli algoritmi. Copre tutti gli argomenti fondamentali: strutture dati, algoritmi di ordinamento e ricerca, teoria dei grafi e molto altro. Include esercizi e soluzioni.',
        99.99,
        'introduzione-algoritmi-cormen.jpg',
        'Libro Introduzione agli Algoritmi',
        30
    ),
    (
        '850e8400-e29b-41d4-a716-506655440004',
        2,
        'Timer pomodoro',
        'Timer digitale progettato specificamente per la tecnica del pomodoro. Aiuta a massimizzare la concentrazione durante lo studio. Include suoneria regolabile, display LED e batteria ricaricabile USB.',
        16.99,
        'timer-pomodoro.png',
        'Timer pomodoro - Timer digitale progettato specificamente per la tecnica del pomodoro. Aiuta a massimizzare la concentrazione durante lo studio. Include suoneria regolabile, display LED e batteria ricaricabile USB.',
        75
    ),
    (
        '950e8400-e29b-41d4-a716-506655440005',
        2,
        'Cocina Fortunellina',
        'Esemplare di una esotica forma di cocinella chiamata cocina fortunellina',
        499.99,
        'cocina-fortunellina.webp',
        'Cocina fortunallina - Esemplare di una esotica forma di cocinella chiamata cocina fortunellina',
        10
    );

-- Associazione Prodotti-Categorie
INSERT INTO
    `product_category` (`product_id`, `category_id`)
VALUES (
        '550e8400-e29b-41d4-a716-446655440000',
        3
    ),
    (
        '450e8400-e29b-41d4-a716-506655440002',
        4
    ),
    (
        '750e8400-e29b-41d4-a716-506655440003',
        2
    ),
    (
        '850e8400-e29b-41d4-a716-506655440004',
        3
    ),
    (
        '950e8400-e29b-41d4-a716-506655440005',
        3
    );

-- Inserimento Recensioni
INSERT INTO
    `reviews` (
        `client_id`,
        `product_id`,
        `title`,
        `content`,
        `rating`,
        `review_date`
    )
VALUES (
        1,
        '550e8400-e29b-41d4-a716-446655440000',
        'Ha salvato la mia sessione estiva',
        'Non avrei mai creduto di poter passare Fisica Teorica, eppure è successo! Il santino ha funzionato anche quando avevo studiato pochissimo. Spedizione veloce e prodotto ben confezionato.',
        5,
        '2025-07-05 14:22:00'
    ),
    (
        1,
        '450e8400-e29b-41d4-a716-506655440002',
        'Buona ma non eccezionale',
        'Fa il suo lavoro, ma dopo un mese alcuni tasti sono diventati duri da premere. Comunque le funzioni sono tutte presenti e il prezzo è accettabile.',
        3,
        '2025-06-15 09:15:00'
    ),
    (
        1,
        '750e8400-e29b-41d4-a716-506655440003',
        'La bibbia di algoritmi',
        'Spiegazioni chiare e dettagliate. Gli esercizi sono impegnativi ma utili per consolidare la conoscenza. Unico difetto: il peso del libro, quasi impossibile da portare in università.',
        4,
        '2025-05-20 11:30:00'
    ),
    (
        1,
        '850e8400-e29b-41d4-a716-506655440004',
        'Ha cambiato il mio modo di studiare',
        'Da quando uso questo timer, la mia produttività è aumentata del 50%. Facile da usare, batteria che dura giorni e suono gradevole. Lo consiglio a tutti gli studenti!',
        5,
        '2025-06-08 10:20:00'
    ),
    (
        1,
        '950e8400-e29b-41d4-a716-506655440005',
        'Costosa ma porta fortuna',
        'L\'ho comprata prima della sessione e ho superato tutti gli esami. Coincidenza? Non credo proprio! Bellissima da vedere sulla scrivania, anche se il prezzo è decisamente alto.',
        4,
        '2025-06-25 15:30:00'
    );

-- Inserimento Ordini
INSERT INTO
    `orders` (
        `client_id`,
        `order_date`,
        `expected_pickup_date`,
        `status`,
        `isPaid`
    )
VALUES (
        1,
        '2025-07-01 00:00:00',
        '2025-07-08 00:00:00',
        'completed',
        1
    ),
    (
        1,
        '2025-07-08 00:00:00',
        '2025-07-15 00:00:00',
        'processing',
        1
    ),
    (
        1,
        '2025-07-12 00:00:00',
        '2025-07-19 00:00:00',
        'pending',
        0
    );

-- Inserimento Dettagli Ordini
INSERT INTO
    `order_details` (
        `order_id`,
        `product_id`,
        `quantity`,
        `purchase_unit_price`
    )
VALUES (
        1,
        '550e8400-e29b-41d4-a716-446655440000',
        2,
        99.99
    ),
    (
        1,
        '850e8400-e29b-41d4-a716-506655440004',
        1,
        16.99
    ),
    (
        1,
        '450e8400-e29b-41d4-a716-506655440002',
        1,
        19.99
    ),
    (
        2,
        '950e8400-e29b-41d4-a716-506655440005',
        1,
        499.99
    ),
    (
        2,
        '750e8400-e29b-41d4-a716-506655440003',
        1,
        99.99
    ),
    (
        3,
        '450e8400-e29b-41d4-a716-506655440002',
        2,
        19.99
    ),
    (
        3,
        '850e8400-e29b-41d4-a716-506655440004',
        1,
        16.99
    );

INSERT INTO
    `notifications` (
        `user_id`,
        `message`,
        `sent_date`,
        `status`,
        `reference`
    )
VALUES (
        1,
        'L\'ordine #1 è stato completato.',
        '2025-07-02 09:00:00',
        'read',
        '1'
    ),
    (
        1,
        'L\'ordine #2 è ora in lavorazione.',
        '2025-07-09 11:00:00',
        'read',
        '2'
    ),
    (
        1,
        'L\'ordine #3 è ora in attesa.',
        '2025-07-13 12:00:00',
        'unread',
        '3'
    );