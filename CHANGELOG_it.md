# 2.x ramo
## 2.0 ramo
### 2.0.0-beta
* tutto il codice è stato interamente riscritto per CakePHP 3.x. Sono state applicate svariate ottimizzazioni;
* per le azioni di caricamento/aggiunta di file, i messaggi di errore sono maggiormente esplicativi;
* aggiunta la classe `MeEmail` per semplificare l'invio delle email;
* aggiunta la classe `MeRequest` per gestire le richieste HTTP;
* aggiunto `ThumbHelper` per la generazione delle miniature;
* aggiornato Bootstrap alla versione 3.3.5 e Moment.js alla versione 2.10.3.

# 1.x ramo
## 1.2 ramo
### 1.2.2
* supporto completo per reCAPTCHA. Riscritto il `RecaptchaComponent`;
* risolto un bug per il Datepicker;
* aggiornato Bootstrap alla versione 3.3.4;
* aggiornato Bootstrap 3 Date/Time Picker alla versione 4.7.14;
* aggiornato PHP Markdown alla versione 1.5.0.

### 1.2.1
* supporto completo per reCAPTCHA;
* il componente e il modello `Token` sono stati interamente riscritti;
* risolto un bug per il metodo `analytics()`.

### 1.2.0
* aggiornato Font Awesome alla versione 4.3.0;
* supporto per FancyBox;
* risolto un bug nelle viste degli errori;
* aggiunti alcuni metodi all'utility `Plugin`. L'utility `System` è stata divisa in più utility;
* aggiungo il file di changelog.