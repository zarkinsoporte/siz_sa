<!DOCTYPE html>
<html>

<head>
 <meta charset="utf-8">
    <title>{{$titulo}}</title>
    <style>
    /*
	Generic Styling, for Desktops/Laptops 
	*/
    img {
        display: block;
    margin-left: 70px;
    width:90%;
    height: 50px;
        position: absolute;
    }
	table { 
		width: 100%; 
		border-collapse: collapse; 
        font-family:arial;
	}
    
</style>
    <script>
        function subst() {
          var vars = {};
          var query_strings_from_url = document.location.search.substring(1).split('&');
          for (var query_string in query_strings_from_url) {
              if (query_strings_from_url.hasOwnProperty(query_string)) {
                  var temp_var = query_strings_from_url[query_string].split('=', 2);
                  vars[temp_var[0]] = decodeURI(temp_var[1]);
              }
          }
          var css_selector_classes = ['page', 'frompage', 'topage', 'webpage', 'section', 'subsection', 'date', 'isodate', 'time', 'title', 'doctitle', 'sitepage', 'sitepages'];
          for (var css_class in css_selector_classes) {
              if (css_selector_classes.hasOwnProperty(css_class)) {
                  var element = document.getElementsByClassName(css_selector_classes[css_class]);
                  for (var j = 0; j < element.length; ++j) {
                      element[j].textContent = vars[css_selector_classes[css_class]];
                  }
              }
          }
      }
    </script>
</head>

<body style="border:0; margin: 0;" onload="subst()>

   <div id="header">
   <table style="">
    <tr>
        
        <td style="text-align:left">
            {{$fechaActualizado}}
        </td>
        <td class="section"></td>
        <td style="text-align:right">
            {{$fechaImpresion}}
        </td>
    </tr>
</table>
<br>
    <img src="{{ url('/images/Mod01_Produccion/siz1.png') }}" >
    <table>
        <tr style="background-color: white">
            <td colspan="2" align="center" bgcolor="#fff">
                <b>{{env('EMPRESA_NAME')}}</b><br>
                <h3>{{$titulo}}</h3>
            </td>
        </tr>
        
    </table>



</div>

</body>
</html>