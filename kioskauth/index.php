<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
  <title>KioskAuth</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <link rel="stylesheet" type="text/css" href="static/adm.css" />
  <link rel="stylesheet" type="text/css" href="static/jquery/jquery-ui-1.10.2.custom.css" />
  <script type="text/javascript" src="static/jquery/jquery-1.9.1.js"></script>
  <script type="text/javascript" src="static/jquery/jquery-ui-1.10.2.custom.min.js"></script>
</head>

<body>

<div class="title">
  <div>KioskAuth</div>
  <div style="font-size: 12px">Tableau de bord</div>
  <div class="button"><a href="kiosk.php">Lancer l'application Kiosk</a></div>
</div>

<div id="tabs">
  <ul>
    <li><a href="#create">Création de compte</a></li>
    <li><a href="#enable">Activation de compte</a></li>
    <li><a href="#ldap">Profil étudiant</a></li>
    <li><a href="#analyse">Analyse de carte</a></li>
    <li><a href="#config">Configuration</a></li>
  </ul>
  <div id="create">
    <h2>Saisissez un numéro étudiant ou insérez une carte.</h2>
    <p><input type="text" id="inCreate" /> <input type="button" value="Créer" onclick="create()" /> &nbsp; <a href="#" onclick="$('#inCreate').val(''); create(); return event.preventDefault();">Effacer et créer</a></p>
	<p><span style="color: #bbb">Gardez le champs vide pour une saisie automatique par carte.</span></p>
    <table cellspacing="8">
      <tr>
        <td><input type="checkbox" checked="checked" id="printChk" /></td>
        <td>
          <label for="printChk">Imprimer la charte et les informations de connexion</label><br />
        </td>
      </tr>
    </table>
  </div>
  <div id="enable">
    <h2>Saisissez un numéro étudiant ou insérez une carte.</h2>
    <p><input type="text" id="inEnable" /> <input type="button" value="Activer" onclick="enable()" /> &nbsp; <a href="#" onclick="$('#inEnable').val(''); enable(); return event.preventDefault();">Effacer et activer</a></p>
    <p><span style="color: #bbb">Gardez le champs vide pour une saisie automatique par carte.</span></p>
  </div>
  <div id="ldap">
    <h2>Saisissez un numéro étudiant ou insérez une carte.</h2>
    <p><input type="text" id="inLdap" /> <input type="button" value="Afficher" onclick="ldap()" /> &nbsp; <a href="#" onclick="$('#inLdap').val(''); ldap(); return event.preventDefault();">Effacer et afficher</a></p>
    <p><span style="color: #bbb">Gardez le champs vide pour une saisie automatique par carte.</span></p>
  </div>
  <div id="analyse">
    <h2>Insérez une carte étudiant à analyser.</h2>
    <p><input type="button" value="Analyser" onclick="analyse()" /></p>
  </div>
  <div id="config">
    <fieldset>
      <legend>Tableau de bord</legend>
      <h2>Lecteur de carte</h2>
      <p><select id="readersAdm"></select></p>
      <h2>Imprimer vers</h2>
      <p><select id="printersAdm"></select></p>
      <table cellspacing="8">
        <tr>
          <td><input type="checkbox" checked="checked" id="ticketChk" /></td>
          <td>
            <label for="ticketChk">Utiliser le format ticket pour cette imprimante</label><br />
          </td>
        </tr>
      </table>
    </fieldset>
    <fieldset>
      <legend>Kiosk</legend>
      <h2>Lecteur de carte</h2>
      <p><select id="readersKiosk"></select></p>
      <h2>Imprimer vers</h2>
      <p><select id="printersKiosk"></select></p>
      <table cellspacing="8">
        <tr>
          <td><input type="checkbox" checked="checked" id="ticketChk" /></td>
          <td>
            <label for="ticketChk">Utiliser le format ticket pour cette imprimante</label><br />
          </td>
        </tr>
      </table>
    </fieldset>
    <p><input type="button" value="Enregistrer" onclick="config()" /></p>
  </div>
</div>

<div id="message" class="notification">
  En attente d'une action à exécuter...
</div>

<pre id="out" class="minibox">&nbsp;</pre>

<script type="text/javascript">

function reader()
{
  var uid = '';
  
  $.ajax({
    dataType: "json",
    type: 'GET',
    url: 'api_reader.php',
    async: false,
    success: function(data, textStatus, jqXHR)
    {
      $('#out').html(ObjectDump(data));
      
      if (data['status'] == 'success')
      {
        uid = data['uid'];
      }
	  else if (data['status'] == 'noreader')
      {
        $('#message').html("Aucun lecteur de carte disponible.");
        $('#message').attr('class', 'error');
      }
      else if (data['status'] == 'nocard')
      {
        $('#message').html("Aucune carte détectée.");
        $('#message').attr('class', 'error');
      }
      else if (data['status'] == 'invalid')
      {
        $('#message').html("Carte illisible ou non supportée.");
        $('#message').attr('class', 'error');
      }
      else
      {
        $('#message').html("Erreur ApiReader : impossible de récupérer les informations de la carte.");
        $('#message').attr('class', 'error');
      }
    },
    error: function()
    {
      $('#message').html("Erreur QueryReader : impossible de récupérer les informations de la carte.");
      $('#message').attr('class', 'error');
    }
  });
  
  return uid;
}

function create()
{
  $('#message').html("Création du compte...");
  $('#message').attr('class', 'notification');
  
  uid = $('#inCreate').val();
  
  if (uid == '')
  {
    uid = reader();
  }
  
  if (uid != '')
  {
    $.ajax({
      dataType: "json",
      type: 'GET',
      url: 'api_create.php',
      data: { uid: uid },
      success: function(data, textStatus, jqXHR)
      {
        $('#out').html(ObjectDump(data));
        
        if (data['status'] == 'enabled')
        {
          $('#message').html("Le mot de passe a été réinitialisé.");
          $('#message').attr('class', 'success');

          if ($('#printChk').is(":checked"))
          {
            printer(uid, data['password'], 'recovery');
          }
        }
        else if (data['status'] == 'created')
        {
          $('#message').html("Le compte a été créé.");
          $('#message').attr('class', 'success');

          if ($('#printChk').is(":checked"))
          {
            printer(uid, data['password'], 'new');
          }

        }
        else if (data['status'] == 'disabled')
        {
          $('#message').html("Action impossible pour un compte désactivé.");
          $('#message').attr('class', 'error');
        }
        else
        {
          $('#message').html("Erreur ApiCreate : impossible de créer le compte.");
          $('#message').attr('class', 'error');
        }
      },
      error: function()
      {
        $('#message').html("Erreur QueryCreate : impossible de créer le compte.");
        $('#message').attr('class', 'error');
      }
    });
  }
}

function enable()
{
  $('#message').html("Activation du compte...");
  $('#message').attr('class', 'notification');
  
  uid = $('#inEnable').val();
  
  if (uid == '')
  {
    uid = reader();
  }
  
  if (uid != '')
  {
    $.ajax({
      dataType: "json",
      type: 'GET',
      url: 'api_enable.php',
      data: { uid: uid },
      success: function(data, textStatus, jqXHR)
      {
        $('#out').html(ObjectDump(data));
        
        if (data['status'] == 'success')
        {
          $('#message').html("Compte activé avec succès.");
          $('#message').attr('class', 'success');
        }
        else
        {
          $('#message').html("Erreur ApiEnable : impossible d'activer le compte.");
          $('#message').attr('class', 'error');
        }
      },
      error: function()
      {
        $('#message').html("Erreur QueryEnable : impossible d'activer le compte.");
        $('#message').attr('class', 'error');
      }
    });
  }
}

function ldap(uid)
{
  $('#message').html("Récupération de la fiche dans l'annuaire UPMC...");
  $('#message').attr('class', 'notification');
  
  uid = $('#inLdap').val();
  
  if (uid == '')
  {
    uid = reader();
  }
  
  if (uid != '')
  {
    $.ajax({
      dataType: "json",
      type: 'GET',
      url: 'api_ldap.php',
      data: { uid: uid },
      success: function(data, textStatus, jqXHR)
      {
        $('#out').html(ObjectDump(data));
        
        if (data['status'] == 'success')
        {
          $('#message').html("Profile récupéré avec succès.");
          $('#message').attr('class', 'success');
        }
        else
        {
          $('#message').html("Erreur ApiLdap : impossible de récupérer les informations dans l'annuaire.");
          $('#message').attr('class', 'error');
        }
      },
	    error: function()
      {
        $('#message').html("Erreur QueryLdap : impossible de récupérer les informations dans l'annuaire.");
        $('#message').attr('class', 'error');
      }
    });
  }
}

function printer(uid, password, type)
{
  $('#message').html("Impression des informations de connexion en cours...");
  $('#message').attr('class', 'notification');
  
  $.ajax({
    dataType: "json",
    type: 'GET',
    url: 'api_printer.php',
	  data: { uid: uid, password: password, givenname: '', sn: '', type: type },
    success: function(data, textStatus, jqXHR)
    {
      $('#out').html(ObjectDump(data));
      
      if (data['status'] == 'success')
      {
        $('#message').html("Impression du document en cours.");
        $('#message').attr('class', 'success');
      }
      else
      {
        $('#message').html("Erreur ApiPrinter : impossible d'imprimer le document.");
        $('#message').attr('class', 'error');
      }
    },
    error: function()
    {
      $('#message').html("Erreur QueryPrinter : impossible d'imprimer le document.");
      $('#message').attr('class', 'error');
    }
  });
}

function analyse()
{
  $('#message').html("Récupération des données de la carte...");
  $('#message').attr('class', 'notification');
  
  $.ajax({
    dataType: "json",
    type: 'GET',
    url: 'api_analyse.php',
    success: function(data, textStatus, jqXHR)
	{
      $('#out').html(ObjectDump(data));
      
	  if (data['status'] == 'success')
      {
	    $('#message').html("Données de la carte récupérées avec succès.");
		$('#message').attr('class', 'success');
	  }
	  else if (data['status'] == 'noreader')
      {
        $('#message').html("Aucun lecteur de carte disponible.");
        $('#message').attr('class', 'error');
      }
      else if (data['status'] == 'nocard')
      {
        $('#message').html("Aucune carte détectée.");
        $('#message').attr('class', 'error');
      }
      else if (data['status'] == 'invalid')
      {
        $('#message').html("Carte illisible ou non supportée.");
        $('#message').attr('class', 'error');
      }
	    else
      {
	      $('#message').html("Erreur ApiAnalyse : impossible de récupérer les données de la carte.");
		    $('#message').attr('class', 'error');
	    }
	  },
    error: function()
    {
      $('#message').html("Erreur QueryAnalyse : impossible de récupérer les données de la carte.");
      $('#message').attr('class', 'error');
    }
  });
}

function config()
{
  alert("Cette fonction n'est pas encore implémentée. Actuellement, seul le premier lecteur de la liste est utilisé et seule l'imprimante par défaut est utilisée.");
}

function load()
{
  $.ajax({
    dataType: "json",
    type: 'GET',
    url: 'api_config.php',
    success: function(data, textStatus, jqXHR)
    {
      if (data['status'] == 'success')
      {
        $.each(data['readers'], function(index, value) {
          $('#readersAdm').append(new Option(value, index, true, true));
          $('#readersKiosk').append(new Option(value, index, true, true));
        });

        $.each(data['printers'], function(index, value) {
          $('#printersAdm').append(new Option(value['NAME'], value['NAME'], true, true));
          $('#printersKiosk').append(new Option(value['NAME'], value['NAME'], true, true));
        });
      }
      else
      {
        // error
      }
    },
    error: function()
    {
      // error
    }
  });
}

function ObjectDump(obj)
{
  this.result = "";
  this.indent = -2;
 
  this.dumpLayer = function(obj)
  {
    this.indent += 2;
 
    for (var i in obj)
    {
      if (typeof(obj[i]) == "object")
      {
        this.result += "              ".substring(0,this.indent)+i+": "+"\n";
        this.dumpLayer(obj[i]);
      }
      else
      {
        this.result += "              ".substring(0,this.indent)+i+": "+obj[i]+"\n";
      }
    }
    
    this.indent -= 2;
  }
  
  this.dumpLayer(obj);
  return this.result;
}

$(document).ready(function() {
  $( "#tabs" ).tabs();
  load();
});

</script>
  
</body>
</html>
