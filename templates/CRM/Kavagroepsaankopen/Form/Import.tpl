
<div class="help">
  <p>Instructies:</p>
  <ol>
    <li>Ga naar de KAVA website</li>
    <li>Klik op Structuur &gt; Formulieren</li>
    <li>Filter de lijst op categorie "Groepsaankopen"</li>
    <li>Voor de gewenste groepsaankoop, klik op het aantal in de kolom "Resultaten" </li>
    <li>Klik op de link "Downloaden"</li>
    <li>Klik onderaan op de knop "Downloaden" (je hoeft geen instellingen te wijzigen op dit scherm)</li>
  </ol>
  <p>Bovenstaande actie zal de inzendingen downloaden in een csv-bestand.</p>
  <p>Dit csv-bestand kan je hier importeren.</p>
</div>

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
