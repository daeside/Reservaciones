function crearSelect(link, id)
{
  var Options='<option value="" disabled="" selected="">Punto</option>';

  $.ajax(
  {
    type: "POST",
    url: link,
    success: function(response)
    {
      $.each(JSON.parse(response), function(i, val)
      { 
        Options += "<option value='" + parseInt(val.id) +"'>" + String(val.nombre) + "</option>";
        $('#' + id).empty();
        $('#' + id).append(Options);
        $('#' + id).formSelect()
      });
    }
  }); 
}

function selectHora(link, id)
{
  var Options='<option value="" disabled="" selected="">Hora</option>';

  $.ajax(
  {
    type: "POST",
    url: link,
    success: function(response)
    {
      $.each(JSON.parse(response), function(i, val)
      { 
        Options += "<option value='" + parseInt(val.id) +"'>" + String(val.hora) + "</option>";
        $('#' + id).empty();
        $('#' + id).append(Options);
        $('#' + id).formSelect()
      });
    }
  }); 
}

function input(id)
{
  var campo = $('#' + id).val();
  if(campo == null || campo == "" || campo.lenght == 0 || /^\s+$/.test(campo))
  {
    return false;
  }

  return true;
}

function password(psw, pswconf)
{
  var campo = $('#' + psw).val();
  var campo2 = $('#' + pswconf).val();

  if(campo != campo2)
  {
    return false;
  }

  return true;
}

function select(campo)
{
  var cmbSelector = document.getElementById(campo).selectedIndex;
  if(cmbSelector == null || cmbSelector == 0)
  {
    return false;
  }

  return true;
}

function login(id, url, response)
{
  $.ajax(
  {                        
    type: "POST",                 
    url: url,                     
    data: $("#" + id).serialize(), 
    success: function(data)             
    {
      if(data == 1)
      {
        location.href = response;
      }
      else
      {
        alert(data);
      }              
    }
  });
}

function send(id, url, message, response)
{
  $.ajax(
  {                        
    type: "POST",                 
    url: url,                     
    data: $("#" + id).serialize(), 
    success: function(data)             
    {
      if(data == 1)
      {
        alert(message);
        location.href = response;
      }
      else
      {
        alert(data);
      }              
    }
  });
}

function tabla(link, id, idioma, columnas)
{
   $('#' + id).dataTable(
    {
      "processing": true,
      "serverSide": false,
      "ajax": 
      {
        "url": link
      },
      "language": 
        {
           "url": idioma
        },
      "columns": columnas
   });
}

function calendario(date)
{
  $('.datepicker').datepicker(
  {
    format: 'yyyy-mm-dd',
    minDate: new Date(date),
    i18n: 
    {
      cancel: 'cancelar',
      done: 'aceptar',
      months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
      monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
      weekdays: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
      weekdaysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
      weekdaysAbbrev: ["D", "L", "M", "M", "J", "V", "S"]
    }
  });
}

function hideshow(id)
{
  $("#" + id).removeClass("hide");
  $("#" + id).addClass("visible");
}

function showhide(id)
{
  $("#" + id).removeClass("visible");
  $("#" + id).addClass("hide");
}

function tipoReserva(dato, nombresel, folio, cuarto, nombre, cfolio, ccuarto, cnombre)
{
  switch(dato)
  {
    case '1':
    $('#' + folio).attr('value', 1);
    $('#' + cfolio).attr('style', 'visibility: hidden;');
    $('#' + cuarto).removeAttr('value');
    $('#' + ccuarto).removeAttr('style');
    $('#' + cnombre).removeAttr('style');
    $('#' + nombre).removeAttr('value');
    break;

    case '2':
    $('#' + cuarto).attr('value', 1);
    $('#' + ccuarto).attr('style', 'visibility: hidden;');
    $('#' + folio).removeAttr('value');
    $('#' + cfolio).removeAttr('style');
    $('#' + cnombre).removeAttr('style');
    $('#' + nombre).removeAttr('value');
    break;

    case '3':
    $('#' + cfolio).attr('style', 'visibility: hidden;');
    $('#' + ccuarto).attr('style', 'visibility: hidden;');
    $('#' + cnombre).attr('style', 'visibility: hidden;');
    $('#' + folio).attr('value', '0001221');
    $('#' + cuarto).attr('value', 1);
    $('#' + nombre).attr('value', nombresel);
    break;

    case '4':
    $('#' + cfolio).attr('style', 'visibility: hidden;');
    $('#' + ccuarto).attr('style', 'visibility: hidden;');
    $('#' + cnombre).attr('style', 'visibility: hidden;');
    $('#' + folio).attr('value', '0001221');
    $('#' + cuarto).attr('value', 1);
    $('#' + nombre).attr('value', nombresel);
    break;
  }
}