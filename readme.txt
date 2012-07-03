Sorry, everything in Lithuanian here. Use Google translate to read this text.

-----

KAS ČIA YRA?

Tai mini balsavimo sistema, labiau skirta technologijos demonstravimo tikslais. Joje kiekvienas gali balsuoti, jei tik administratorius yra parinkęs vieną balsavimą aktyviu.

LICENZIJA

Skaitykite license.txt failiuką norėdami sužinoti plačiau.

ĮDIEGIMAS

Jei konfiguraciniai duomenys nebus tinkami veikimui automatiškai bus paleistas įdiegėjas, todėl norėdami įdiegti šią svetainę kitame serveryje, tiesiog sukėlę failus atidarykite ją naršyklėje ir įvykdykite visus nurodymus.

STIPRIOSIOS PUSĖS

 * lentelių struktūros sugeneruojamos automatiškai, priklausomai nuo to, kokie objektai yra class/objects kataloge bei kokias savybes turi - nereikia SQL žinių perkeliant visa tai ar juos atkūrinėti pametus duombazę
 * naudojama Controls idėja (pan. kaip ActiveX, Zarilia Controls...), todėl puslapių pagrindinė logika yra aprašoma beveik kone puslapio šablonuose - tai leidžia sutaupyti nemažai laiko, kuriant geresnius GUI, bei leidžia techniniams dizaineriams naudotis galimybėmis, kurios šiaip pasiekiamos būna tik programuotojams. Beto ir programuotojams lengvai atlikti su kai kuriais objektais Ajax'o reikalaujančias operacijas.
 * LiveUpdate palaikoma kone visur - tinklalapio lankytojai pakeitimus pamato kone iškarto kai tik jie įvyksta (pvz. pakeičiama apklausa), todėl nereikia perkrovinėti puslapio norint pamatyti atnaujinimus (šią funkciją taip pat galima išbandyti atsidarius dviejų naršyklių langus ir aktyvuojant skirtingas apklausas arba balsuojant)
 * automatiškai pasileidžiantis įdiegėjas - nereikia sukti galvos kaip visa tai įdiegti
 * galima atsiųsti visą tinklalapio kodą kaip archyvą - nereikia klausinėti iš kur jį gauti :)
 * programuotojams - visos klasės gali būti išplėčiamos net keliomis klasėmis (vietoj. extend atributo naudojimo), jei šios klasės patalpintos class/support kataloge ir klasė - galima daug lengviau atskirti logiką, kuri iš dalies sutampa tarp objektų.
 * apklausų redagavime naudojamas WYSIWYG tipo redaktorius - lengva matyti kaip maždaug atrodys apklausa vartotojų pusėje (beto galima lengvai atsakymus tampant stumdyti)
 * duomenų bazei naudojama AdoDB Lite, kurios pagalba teoriškai ši svetainė gali veikti kone su bet kuria duombaze.
 * naudojami Smarty šablonai - jie gana lengvai suprantami kiekvienam mokančiam HTML
 * sisteminės klasės į atmintį įkraunamos automatiškai

SILPNOSIOS PUSĖS

 * LiveUpdate naudojasi Ajax'ais o ne Comet ir nėra optimizuotas - per daug nereikalingų užklausų į serverį bei kartais pastebimai vėluojamas info atvaizdavimas arba atsiranda nelaukti mirksėjimai
 * Usability vidutiniškas: pele viskas kaip ir gerai veikia, bet klaviatūra nedirba visur
 * dėl laiko trūkumo kol kas nepaisant AdoDB Lite bibliotekos, ši svetainė gali veikti tik su MySQL duomenų baze
 * jquery.jqplot „grybauja“ šiek tiek IE - nevisada atvaizduoja gražiai grafikus
 * cache katalogui reikėtų geresnio automatiško valymo, prastai sugeneruotiems failams
 * jei apklausoje kas nors balsavo, negalima redaguoti jų klausimų/atsakymų
 * apklausų WYSIWYG redaktorius, redaguojant ilgą tekstą nevisada korektiškai viską rodo
 * class/support/events.php nėra kol kas niekur naudojama, nors keliose klasėse ji įkraunama į atmintį
 * svetainės įtraukimas į archyvą yra šiek tiek per lėtas
 * neištestuota su visais populiariais serveriais ir OS - gali ne visur veikti taip kaip norėtųsi

KONTAKTAI

Jei turite kokių nors minčių, pataisymų galite parašyti laišką github@mekdrop.name