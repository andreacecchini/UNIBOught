# Progetto Tecnologie Web 2024/2025

## Descrizione del Progetto

Il progetto prevede la realizzazione di una piattaforma di **e-commerce dedicata alla vendita di materiale universitario** all'interno del campus dell'Università di Bologna (Unibo) a Cesena. La piattaforma si propone come punto di riferimento per studenti e docenti, offrendo una vasta gamma di prodotti utili per la vita accademica, tra cui:

- **Articoli di cancelleria** (penne, quaderni, evidenziatori, ecc...)
- **Libri di testo**
- **Appunti e materiale didattico**
- **Strumenti tecnologici** (calcolatrici scientifiche, accessori per computer, ecc...)

### Tipologie di Utenti del Sistema

Il sistema prevede due tipologie principali di utenti, ciascuna con funzioni specifiche:

1. **Utente Venditore**
2. **Utente Cliente**

---

### Funzionalità per l'Utente Venditore

L'**Utente Venditore** ha il compito di gestire la vendita dei prodotti e l'interazione con gli utenti clienti. Le sue principali funzionalità includono:

1. **Gestione del Catalogo Prodotti**:
   - **Aggiungere nuovi prodotti**: Il venditore può inserire nuovi articoli nel catalogo, definendo tutte le informazioni relative, come nome del prodotto, descrizione, categoria, prezzo e disponibilità.
   - **Modificare i dettagli dei prodotti esistenti**: È possibile aggiornare le informazioni di prodotto, come la modifica di prezzi, descrizioni, quantità disponibili, immagini e altre specifiche.
   - **Rimuovere prodotti**: Il venditore può eliminare prodotti non più in vendita o non più disponibili.

2. **Gestione degli Ordini**:
   - **Visualizzazione degli ordini ricevuti**: Il venditore può monitorare gli ordini effettuati dai clienti, visualizzando tutti i dettagli dell'acquisto, come i prodotti ordinati, la quantità, ecc.
   - **Aggiornamento dello stato degli ordini**: Il venditore può cambiare lo stato degli ordini a seconda del loro avanzamento, come ad esempio da "In attesa", "In preparazione", "In consegna", "Disponibile per il prelievo", "Prelevato", "Cancellato".

3. **Gestione delle Notifiche**:
   - **Disponibilità dei prodotti**: Il venditore riceve notifiche quando un prodotto, precedentemente disponibile, è stato esaurito. In questo modo, il venditore può agire tempestivamente, ad esempio rifornendo il prodotto o aggiornando il catalogo.
   - **Nuovi ordini**: Il venditore riceve notifiche per ogni nuovo ordine.

---

### Funzionalità per l'Utente Cliente

L'**Utente Cliente** (lo studente o il docente) ha come obiettivo principale l'acquisto di materiale accademico tramite la piattaforma. Le sue principali funzionalità includono:

1. **Registrazione e Login**:
   - **Creazione di un account**: I clienti possono registrarsi sulla piattaforma creando un account personale, utilizzando il proprio indirizzo email e una password sicura.
   - **Accesso all'account**: I clienti possono accedere al proprio account per monitorare gli ordini effettuati, gestire i propri dati e visualizzare lo storico degli acquisti.

2. **Navigazione nel Catalogo Prodotti**:
   - **Visualizzazione dei prodotti**: I clienti possono esplorare il catalogo di prodotti, visualizzando le informazioni dettagliate di ciascun articolo (descrizione, prezzo, disponibilità, ecc...).
   - **Filtri**: La piattaforma permette di applicare filtri (nome, prezzo e categoria) per facilitare la ricerca dei prodotti desiderati.

3. **Gestione del Carrello**:
   - **Aggiunta di prodotti al carrello**: I clienti possono aggiungere facilmente i prodotti desiderati nel carrello per procedere con l'acquisto in un secondo momento.
   - **Modifica del carrello**: I clienti possono aggiungere, rimuovere o modificare la quantità dei prodotti nel carrello prima di procedere al checkout.

4. **Checkout e Pagamento**:
   - **Finalizzazione dell'acquisto**: Una volta selezionati i prodotti, i clienti possono procedere al checkout per confermare l'ordine e scegliere il metodo di pagamento (con carta oppure al ritiro).
   - **Conferma dell'ordine**: Una volta completato il pagamento, il cliente riceve una conferma d'ordine con i dettagli dell'acquisto e la stima dei tempi di consegna.

5. **Notifiche e Aggiornamenti dello Stato dell'Ordine**:
   - **Monitoraggio dello stato dell'ordine**: I clienti possono visualizzare in tempo reale lo stato del proprio ordine tramite il loro account, ottenendo informazioni come "In Attesa", "In Lavorazione", "Spedito" o "Completato".
   - **Listino prodotti**: I clienti vengono avvisati se un prodotto nel proprio carrello è in shortage oppure è stato rimosso temporaneamente dal listino.

6. **Recensioni dei Prodotti**:
   - **Recensione del prodotto**: Una volta ritirato e utilizzato il prodotto, l'utente cliente ha la possibilità di **lasciare una recensione** sul prodotto acquistato. La recensione può includere una valutazione da 1 a 5 stelle, insieme a un commento che esprima la propria opinione riguardo qualità, utilità e soddisfazione complessiva.
   - **Consultazione delle recensioni**: I clienti possono consultare le recensioni lasciate da altri utenti per valutare meglio l'acquisto di un prodotto.

---

## Come Avviare il Progetto

Per avviare il progetto, segui i passaggi seguenti:

### 1. Clonare il Repository

Clona il repository sul tuo computer locale:

```bash
git clone https://github.com/andreacecchini/progetto-tec-web.git
cd progetto-tec-web
```

### 2. Verificare di avere Docker installato

Assicurati di avere Docker e Docker Compose installati sul tuo sistema.

```bash
docker --version
docker-compose --version
```

### 3. Avviare i Servizi

Esegui il comando seguente per avviare i servizi:

```bash
# Se i container non esistono
docker-compose -f docker/docker-compose.yml up -d
# Se i container sono già presenti
docker-compose -f docker/docker-compose.yml start
```

### 4. Accedere all'Applicazione

Una volta avviati i servizi, puoi accedere all'applicazione visitando `http://localhost:8080` nel tuo browser.

### 5. Login

All'inizio vengono messi a disposizione due utenti di prova:

1. Account cliente: email: **<test@unibo.it>** pwd: **testtest1**
2. Account venditore: email: **<vendor@unibo.it>** pwd: **testtest1**

### 6. Chiusura dei servizi

Per interrompere i servizi precedentemente avviati, è possibile utilizzare uno dei seguenti comandi:

1. ```bash
   # Rimuove i container dove vengono eseguiti i servizi e i relativi volumi
   docker-compose -f docker/docker-compose.yml down -v
   ```

2. ```bash
   # Spegne i container senza rimuoverli
   docker-compose -f docker/docker-compose.yml stop
   ```
