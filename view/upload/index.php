<section class="part">
    <h2 class=part__title>Stap 1: Exporteer een conversatie</h2>
    <p>U dient eerst de conversatie te exporteren uit Whatsapp.</p>
    <ol class="steps">
        <li>Lees de documentatie rond het exporteren van conversaties voor <a href="https://faq.whatsapp.com/en/android/23756533/?lang=nl">Android</a> of <a href="https://faq.whatsapp.com/en/iphone/26000285?lang=nl">iOS</a></li>
        <li>Exporteer ook alle media items</li>
        <li>iOs zal normaal gezien direct een zip file aanbieden.  Voor Android dien je alle bestanden nog in een map te plaatsen en deze map te zippen.  Voor Android exporteer je best naar Drive in plaats van via mail.</li>
    </ol>
    <!--
    <article class="subpart">
        <h3 class="subpart__title">Optie 2: via de website</h3>
        <p>U kan een conversatie van <a href="https://web.whatsapp.com/">web.whatsapp.com</a> exporteren door gebruik te maken van een userscript binnen <strong>Google Chrome</strong>. U dient dit script éénmalig te installeren.  Voer hiervoor onderstaande stappen uit.</p>
        <ol class="steps">
            <li>Installeer de userscript manager <a href="https://chrome.google.com/webstore/detail/tampermonkey/dhdgffkkebhmkfjojejmpbldmpobfkfo?hl=nl">TamperMonkey</a> als Chrome extension.</li>
            <li>Installeer het whatsapp scraper script: <a href="https://github.com/frederikduchi/whatsapp-scraper/raw/master/whatsapp-scraping.user.js">whatsapp-scraper.js</a></li>
        </ol>
        <p>Na installatie van het script kan u het telkens gebruiken om een conversatie te downloaden</p>
        <ol class="steps">
            <li>Surf naar <a href="https://web.whatsapp.com">https://web.whatsapp.com</a> en meld aan door de QR code te scannen.</li>
            <li>Selecteer een conversatie en klik op de download button die door het userscript bovenaan werd toegevoegd</li>
            <li>Wacht tot de zip file wordt gedownload.  Voor lange conversaties kan dit enkele minuten duren.</li>
        </ol>
    </article>
    -->
</section>

<section class="part">
    <h2 class="part__title">Stap 2: Upload de conversatie</h2>
    <p>Upload de zip-file die de export van een conversatie bevat</p>
    <form action="index.php?page=upload" method="post" enctype="multipart/form-data" class="upload-form">
        <input type="hidden" name="action" value="startupload">
        <input type="file" name="conversation-zip" class="button upload-file" accept="application/zip">
        <input type="submit" value="start uploaden" class="button upload-button ">
    </form>
    <p class="upload-status info-box info hidden">Uploaden is gestart, afhankelijk van de bestandsgrootte kan dit even duren</p>
</section>

<section class="part">
    <h2 class="part__title">Stap 3: Bekijk de analyse</h2>
    <p>Als het uploaden van de conversatie succesvol verlopen is krijgt u de analyse automatisch te zien. U kan deze analyse later opnieuw bekijken indien u de url bewaart.</p>
</section>

