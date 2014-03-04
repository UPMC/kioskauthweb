
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
      addConsole(ObjectDump(data));
      
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
  
  uid = $('#uid').val();
  
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
        addConsole(ObjectDump(data));
        
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
  
  uid = $('#uid').val();
  
  if (uid == '')
  {
    uid = reader();
    $('#uid').val(uid);
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
        addConsole(ObjectDump(data));
        
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
  
  uid = $('#uid').val();
  
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
        addConsole(ObjectDump(data));
        
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
      addConsole(ObjectDump(data));
      
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
      addConsole("analyse() exécuté avec succès, ObjectDump() des données :");
      addConsole(ObjectDump(data));
      
	    if (data['status'] == 'success')
      {
        $('#uid').val(data['uid']);
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

function execute()
{
  var selected = $('input:radio[name=action]:checked').val();
  
  if (selected == 'create') {
    addConsole("execute() lance l'action create()");
    create();
  }
  else if (selected == 'enable') {
    addConsole("execute() lance l'action enable()");
    enable();
  }
  if (selected == 'ldap') {
    addConsole("execute() lance l'action ldap()");
    ldap();
  }
  if (selected == 'analyse') {
    addConsole("execute() lance l'action analyse()");
    analyse();
  }
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

function addConsole(row) {
  d = new Date();
  
  $('#console').append(d.toISOString()+" # "+row+"\n");
  //$('#console').scrollBottom();
}

function bindActionClick()
{
  selected = $('input:radio[name=action]:checked').val();
  
  addConsole("nouvelle action sélectionnée : "+selected);
    
  if (selected == 'analyse') {
    $('#uid').attr('disabled', 'disabled');
  }
  else {
    $('#uid').removeAttr('disabled');
  }
}

function bindUidKeypress(e)
{
  if (e.which == 13) {
    execute();
    return false;
  }
}

$(document).ready(function() {

  $('input:radio[name=action]').change(function() { bindActionClick() });
  $('#execute').click(function() { execute() });
  $('#uid').keypress(function(e) { return bindUidKeypress(e) });
  $('#close').click(function() { $('#fancy').toggle() });
  
  load();
  
  addConsole("nouvelle instance de l'application");
});