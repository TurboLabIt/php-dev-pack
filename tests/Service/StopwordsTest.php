<?php
namespace TurboLabIt\TLIBaseBundle\tests;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\Service\Stopwords;


class StopwordsTest extends TestCase
{
    public function txtToTestProvider()
    {
        yield [['Un\'altra per di la uno per di qua e l\'altro lui va con l\'altra lei un cane per un\'aspirina', 'cane aspirina']];
        yield [['Scaricare Windows 7 DVD/ISO in italiano: download diretto ufficiale', 'Scaricare Windows 7 DVD/ISO italiano: download diretto ufficiale']];
        yield [['Scaricare Windows 10 DVD/ISO in italiano: download diretto ufficiale (versione 20H2, Ottobre 2020)', 'Scaricare Windows 10 DVD/ISO italiano: download diretto ufficiale (versione 20H2, Ottobre 2020)']];
        yield [['Scaricare Windows XP CD/ISO in italiano: download diretto ufficiale', 'Scaricare Windows XP CD/ISO italiano: download diretto ufficiale']];
        yield [['Siti BitTorrent in italiano 2021: i 10 migliori indici per trovare .torrent ITA (alternative a TNTVillage)', 'Siti BitTorrent italiano 2021: 10 migliori indici trovare .torrent ITA (alternative TNTVillage)']];
        yield [['Guida: installare Windows 10 da chiavetta USB (video)', 'Guida: installare Windows 10 chiavetta USB (video)']];
        yield [['Guida: come formattare e reinstallare Windows 10 nel 2021 (video)', 'Guida: come formattare reinstallare Windows 10 2021 (video)']];
        yield [['Come aggiornare subito a Windows 10 20H2 (Ottobre 2020), anche quando non si trova su Windows Update', 'Come aggiornare Windows 10 20H2 (Ottobre 2020), non trova Windows Update']];
        yield [['Guida Windows 10: come condividere file e cartelle in rete locale (LAN)', 'Guida Windows 10: come condividere file cartelle rete locale (LAN)']];
        yield [['La Grande Guida a Miracast (Wireless Display) - Usare la TV come schermo wireless per PC, tablet e smartphone', 'Guida Miracast (Wireless Display) - Usare TV come schermo wireless PC, tablet smartphone']];
        yield [['Windows 10, Prompt dei comandi: accesso negato - come aprire (sempre) il "Prompt dei comandi" di Amministratore', 'Windows 10, Prompt comandi: accesso negato - come aprire () "Prompt comandi" Amministratore']];
        yield [['Il miglior browser per Windows XP: quali alternative a Google Chrome?', 'miglior browser Windows XP: alternative Google Chrome?']];
        yield [['Guida rapida a Kodi (XBMC) - Il PC come "media center" da salotto', 'Guida rapida Kodi (XBMC) - PC come "media center" salotto']];
        yield [['Saltare l\'inserimento del codice "Product Key" ("il seriale") e scegliere l\'edizione di Windows 8.1 da installare', 'Saltare inserimento codice "Product Key" (" seriale") scegliere edizione Windows 8.1 installare']];
        yield [['Guida alle edizioni di Windows 10: quali differenze fra Windows 10 Home, Windows 10 Pro e Windows 10 Enterprise?', 'Guida edizioni Windows 10: differenze Windows 10 Home, Windows 10 Pro Windows 10 Enterprise?']];
        yield [['[risolto] APN Iliad e Android: come configurare Internet 4G (parametri connessione dati per smartphone)', '[risolto] APN Iliad Android: come configurare Internet 4G (parametri connessione dati smartphone)']];
        yield [['Cosa fare se gli aggiornamenti di Windows Update non funzionano e non si installano', 'Cosa fare aggiornamenti Windows Update non funzionano non installano']];
        yield [['Scaricare Microsoft Office 2016 DVD/ISO in italiano: download diretto ufficiale (retail e volume)', 'Scaricare Microsoft Office 2016 DVD/ISO italiano: download diretto ufficiale (retail volume)']];
        yield [['Scaricare Microsoft Office 2019 DVD/ISO in italiano: download diretto ufficiale', 'Scaricare Microsoft Office 2019 DVD/ISO italiano: download diretto ufficiale']];
        yield [['Windows 10: meglio aggiornare o rimanere con Windows 7 / Windows 8?', 'Windows 10: meglio aggiornare rimanere Windows 7 / Windows 8?']];
        yield [['Come inserire le emoji (faccine) con la tastiera da PC Windows 10', 'Come inserire emoji (faccine) tastiera PC Windows 10']];
    }


    /**
     * @dataProvider txtToTestProvider
     */
    public function testClean($arrTxt)
    {
        $txt = $arrTxt[0];
        //$expected = $arrTxt[1];
        $expected = $arrTxt[1] ?: $txt;
        $actual = (new Stopwords())->clean($txt);

        $this->assertEquals($expected, $actual);
    }
}
