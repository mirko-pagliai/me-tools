# MeTools
MeTools è un plugin per CakePHP per migliorare lo sviluppo delle applicazioni.  
Fornisce alcuni strumenti utili, come componenti, helpers e librerie javascript.

Le API di MeTools sono disponibili [qui](http://repository.novatlantis.it/metools/API) e 
il repository della sandbox è [qui](http://github.com/mirko-pagliai/MeToolsSandbox). 
È possibile testare la sandbox [qui](http://repository.novatlantis.it/metools-sandbox).

## Versionamento
Per la trasparenza e la comprensione del nostro ciclo di rilascio e mantenere la retro-compatibilità,
MeTools viene rilasciato seguendo le [linee guida del versionamento semantico](http://semver.org/lang/it).

## Installazione
Estrarre MeTools in `APP/Plugin` e caricalo in `APP/Config/bootstrap.php`:

	CakePlugin::load(array('MeTools' => array('routes' => TRUE)));

Nella webroot, creare (o copiare) un link alla webroot di MeTools:

	cd APP/webroot
	ln -s ../Plugin/MeTools/webroot/ MeTools

Alcuni file `js` e `css` vengono aggiungi alla fine del layout. Modifica il tuo layout aggiungendo prima
del tag `</body>`:

		<?php
			echo $this->fetch('css_bottom');
			echo $this->fetch('script_bottom');
		?>

## Configurazione
Copiare e rinominare `APP/Plugin/Config/recaptcha.default.php` in `APP/Config/recaptcha.php`,
quindi configurare le chiavi di Recaptha.

## View per gli errori
È possibile utilizzare le view per gli errori fornite da MeTools, copiandole da `APP/Plugin/MeTools/View/Errors`
in `APP/View/Errors`.

Altrimenti è possibile possibile utilizzare la classe `MeExceptionRenderer` per gestire gli errori,
che saranno così visualizzati utilizzando le view fornite da MeTools.  
Per questo, nel file `APP/Config/core.php`, è necessario modificare la configurazione delle eccezioni così:

	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'MeTools.MeExceptionRenderer',
		'log' => true
	));

## Messaggi flash
È possibile utilizzare le view per i messaggi flash fornite da MeTools, copiandole da `APP/Plugin/MeTools/View/Elements`
in `APP/View/Elements`.

Altrimenti è possibile utilizzare il componente `MeSession` per generare i messaggi flash,
che saranno così visualizzati utilizzando le view fornite da MeTools.  
Per questo, bisogna utilizzare il componente come alias, così:
	
	public $components = array('Session' => array('className' => 'MeTools.MeSession'));

Ora è possibile generare messaggi flash in questo modo:
	
	$this->Session->flash('This is an error message', 'error');

## Paginatore
È possibile utilizzare l'helper `MePaginator` per generare i controlli della paginazione,
come i numeri delle pagine e i link successivo/precedente.
Per questo, bisogna utilizzare l'helper come alias, così:

	public $helpers = array('Paginator' => array('className' => 'MeTools.MePaginator'));

Quindi, dentro le viste, utilizzare l'apposito elemento:

	echo $this->element('MeTools.paginator');

## Dump SQL
Per visualizzare il dump SQL, utilizzare l'apposito elemento:

	echo $this->element('MeTools.sql_dump');

Questo visualizzare il dump SQL solo quando disponibile e solo se l'utente non sta utilizzando un dispositivo mobile.

## Compress Shell
La shell `CompressShell` permette di combinare e comprime file css e js.  
Consulta la [pagina](//github.com/mirko-pagliai/MeTools/wiki/Compress-Shell) del nostro wiki.

## reCAPTCHA
Per usare reCAPTCHA, consulta la [pagina](//github.com/mirko-pagliai/MeTools/wiki/reCAPTCHA) del nostro wiki.

## Librerie e script
MeTools include diverse librerie e script:

- jQuery 2.1.3 ([sito](http://jquery.com));
- Bootstrap 3.3.2 ([sito](http://getbootstrap.com)), senza le Glyphicons;
- Font Awesome 4.3.0 ([sito](http://fortawesome.github.com/Font-Awesome));
- PHP Markdown 1.5.0 ([sito](http://michelf.ca/projects/php-markdown));
- reCAPTCHA PHP library 1.11 ([sito](https://developers.google.com/recaptcha));
- Bootstrap 3 Date/Time Picker 4.4.0 ([sito](https://github.com/Eonasdan/bootstrap-datetimepicker));
- Moment.js 2.9.0 ([sito](http://momentjs.com/)), inclusi i "locales".

## CKEditor
MeTools non contiene una copia di CKEditor.

Quindi è necessario scaricare CKEditor dal suo [sito](http://ckeditor.com/download), preribilmente 
[configurando i plugin](http://ckeditor.com/builder).  
Se lo ritieni utile, puoi fare l'upload del file `build-config.js`, che si trova in `APP/Plugin/MeTools/webroot/ckeditor`.
Questo contiene una configurazione valida per molti casi.

Dopo aver scaricato CKEditor, estrarlo in `APP/webroot/ckeditor` o `APP/webroot/js/ckeditor`.  
Infine, modificare il file `ckeditor_init.js` in `APP/Plugin/MeTools/webroot/ckeditor`, utilizzato da MeTools per
instanziare CKEditor. Più semplicemente, copiarlo in `APP/webroot/js`.  
Se MeTools non trova il file `ckeditor_init.js` nella webroot della tua applicazione,
utilizzerà il suo file nella webroot del plugin.

### Come utilizzare CKEditor con MeTools
Utilizzare `LibraryHelper` per caricare gli script di CKEditor scripts. In una view o nel layout dell'applicazione:

	$this->Library->ckeditor();

Se non si desidera utilizzare l'adattatore per jQuery, passare `FALSE` come primo argomento:

	$this->Library->ckeditor(FALSE);

Quindi, dentro una view, creare una textarea di CKEditor utilizzando `MeFormHelper`:

	echo $this->Form->ckeditor('text');

Notare che il metodo `ckeditor()` fornito da `MeFormHelper` utilizza gli stessi argomenti del metodo `input()`, 
incluse le sue opzioni. Per esempio:

	echo $this->Form->ckeditor('text', array(
		'class'	=> 'my_textarea',
		'label' => 'Body',
	));

## FancyBox.
MeTools non contiene una copia di FancyBox.

Quindi è necessario scaricare FancyBox dal suo [sito](http://fancyapps.com/fancybox).

Dopo aver scaricato FancyBox, estrarlo in `APP/webroot/fancybox`.  
Infine, modificare il file `fancybox_init.js` in `APP/Plugin/MeTools/webroot/fancybox`, utilizzato da MeTools per
instanziare FancyBox. Più semplicemente, copiarlo in `APP/webroot/js`.  
Se MeTools non trova il file `fancybox_init.js` nella webroot della tua applicazione,
utilizzerà il suo file nella webroot del plugin.