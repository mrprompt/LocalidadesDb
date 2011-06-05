// variáveis globais
var arVersion   = navigator.appVersion.split("MSIE")
var version     = parseFloat(arVersion[1])
var isValid     = false;

// Gera um select com o array repassado
function generateSelect (selectId, campos, selecionado)
{
    var tmpCampoSel = false;
    var indiceStart = 0;

    while (selectId.options.length > 0) {
        selectId.options[0] = null;
    }

    if (campos != "") {
        for (var i=indiceStart; i < campos.length; i++) {
            tmpCampos       = unescape( campos[i] );
            aTmpCampos      = tmpCampos.split(";");
            tmpCampoId      = aTmpCampos[0];
            tmpCampoValue   = aTmpCampos[1];
            tmpCampoSel     = false;

            if ( tmpCampoId == selecionado) {
                tmpCampoSel = true;
            }

            selectId.options[i] = new Option(tmpCampoValue, tmpCampoId, tmpCampoSel);

            if ( tmpCampoId == selecionado) {
                selectId.options[i].selected = true;
            }
        }
    } else {
        selectId.options[0] = new Option("------------", 0);
    }
}

function buscaPaises ()
{
    new Ajax.Request('mundo.php?param=paises', {
        method: 'get',
        onComplete: function(requisicaoOriginal)
        {
            // Transforma a lista JSON em Javascript
            var p           = $("fgPais");
            var pais        = $F("fgPaisOrig");
            var aCampos     = requisicaoOriginal.responseText.split(",");

            generateSelect(p, aCampos, pais);

            buscaEstados();
        }
    });
}

function buscaEstados ()
{
    new Ajax.Request('mundo.php?param=estados&pais=' + $F('fgPais'), {
        method: 'get',
        onComplete: function (requisicaoOriginal)
        {
            // Transforma a lista JSON em Javascript
            var e           = $("fgEstado");
            var estado      = $F("fgEstadoOrig");
            var aCampos     = requisicaoOriginal.responseText.split(",");

            generateSelect(e, aCampos, estado);

            buscaCidades ()
        }
    });
}

function buscaCidades ()
{
    new Ajax.Request('mundo.php?param=cidades&pais=' + $F("fgPais") + "&estado=" + $F("fgEstado"), {
        method: 'get',
        onComplete: function (requisicaoOriginal)
        {
            // Transforma a lista JSON em Javascript
            var c           = $("fgCidade");
            var cidade      = $F("fgCidadeOrig");
            var aCampos     = requisicaoOriginal.responseText.split(",");

            generateSelect(c, aCampos, cidade);
        }
    });
}

function validateEmail(e)
{
    if(/^[a-zA-Z][\w\.-]*[a-zA-Z0-9]@[a-zA-Z0-9][\w\.-]*[a-zA-Z0-9]\.[a-zA-Z][a-zA-Z\.]*[a-zA-Z]$/.test(e.value))
    {
        if(!isValid)
        {
            $(e).morph('border-color:#00FF00', {
                duration:.3
            });
            isValid = true;
        }
    } 
    else
    {
        if(isValid)
        {
            $(e).morph('border-color:#FF0000', {
                duration:.3
            });
            isValid = false;
        }
    }
}
