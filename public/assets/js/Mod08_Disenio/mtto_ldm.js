 function js_iniciador() {
     $('.toggle').bootstrapSwitch();
     $('[data-toggle="tooltip"]').tooltip();
     $('.boot-select').selectpicker();
     $('.dropdown-toggle').dropdown();
     setTimeout(function () {
         $('#infoMessage').fadeOut('fast');
     }, 5000); // <-- time in milliseconds
     $("#sidebarCollapse").on("click", function () {
         $("#sidebar").toggleClass("active");
         $("#page-wrapper").toggleClass("content");
         $(this).toggleClass("active");
     });
     $("#sidebar").toggleClass("active");
     $("#page-wrapper").toggleClass("content");
     $(this).toggleClass("active");
     document.onkeyup = function (e) {
         if (e.shiftKey && e.which == 112) {
             var namefile = 'RG_' + $('#btn_pdf').attr('ayudapdf') + '.pdf';
             //console.log(namefile)
             $.ajax({
                 url: "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile,
                 type: 'HEAD',
                 error: function () {
                     //file not exists
                     window.open("{{ URL::asset('ayudas_pdf') }}" + "/AY_00.pdf", "_blank");
                 },
                 success: function () {
                     //file exists
                     var pathfile = "{{ URL::asset('ayudas_pdf') }}" + "/" + namefile;
                     window.open(pathfile, "_blank");
                 }
             });


         }
     }
    $('#sel_tipomat').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
    $('#sel_tipomat').val(1);
    $('#sel_tipomat').selectpicker('refresh');
    $('#sel_articulos').selectpicker({
        noneSelectedText: 'Selecciona una opción',
        noneResultsText: 'Ningún resultado coincide',
        countSelectedText: '{0} de {1} seleccionados'
    });
     $("#input_update").click(function () {
         $("#ch1").prop("checked", true);
         $("#ch2").prop("checked", false);
         $("#ch3").prop("checked", false);
     });
     $("#input_modificacion").click(function () {
         $("#ch2").prop("checked", true);
         $("#ch1").prop("checked", false);
         $("#ch3").prop("checked", false);
     });
     //$(window).on('load', function () {
     var wrapper = $('#page-wrapper');
     var resizeStartHeight = wrapper.height();
     var height = (resizeStartHeight * 75) / 100;
     if (height < 200) {
         height = 400;
     }
     /*GENERAR OP*/
     var table = $("#tabla_arts").DataTable({
         language: {
             "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
         },
         scrollX: true,
         scrollY: height,
         dom: 'Brtip',
         buttons: [{
                 text: '<i class="fa fa-check-square"></i>',
                 titleAttr: 'seleccionar',
                 action: function () {
                     table.rows({
                         page: 'current'
                     }).select();
                     var count = table.rows('.selected').count();
                     var $badge = $('#btn_enviar').find('.badge');
                     $badge.text(count);
                 }
             },
             {
                 text: '<i class="fa fa-square"></i>',
                 titleAttr: 'deseleccionar',
                 action: function () {
                     table.rows({
                         page: 'current'
                     }).deselect();
                     var count = table.rows('.selected').count();
                     var $badge = $('#btn_enviar').find('.badge');
                     $badge.text(count);
                 }
             },
             {
                 text: '<i class="fa fa-refresh"></i>',
                 titleAttr: 'recargar',
                 action: function () {
                    if ($("#sel_articulos").val() == '') {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-danger m-b-0'>Seleccionar artículo.</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({
                            'font-size': '14px'
                        });
                    } else {
                        reload_tabla_arts(true);
                        var count = table.rows('.selected').count();
                        var $badge = $('#btn_enviar').find('.badge');
                        $badge.text(count);
                    }
                        
                   
                 }
             }

         ],
         scrollCollapse: true,
         deferRender: true,
         pageLength: -1,
         columns: [{
                 data: "codigo_origen"
             },
             {
                 data: "descripcion_origen"
             },
             
             {
                 data: "um"
             },
             {
                 data: "cantidad"
             },
             {
                 data: "precio"
             }

         ],
         'columnDefs': [{

                 "targets": [3],
                 "searchable": false,
                 "orderable": false,
                 "render": function (data, type, row) {

                     if (row['cantidad'] != '') {

                         return number_format(row['cantidad'], 2, '.', ',');

                     } else {

                         return '';

                     }

                 }

             },
             {

                 "targets": [4],
                 "searchable": false,
                 "orderable": false,
                 "render": function (data, type, row) {

                     if (row['precio'] != '') {

                         return number_format(row['precio'], 2, '.', ',');

                     } else {

                         return '0.00';

                     }

                 }

             }
         ],

     });

     $('#tabla_arts thead tr').clone(true).appendTo('#tabla_arts thead');

     $('#tabla_arts thead tr:eq(1) th').each(function (i) {
         var title = $(this).text();
         $(this).html('<input style="color: black;"  type="text" placeholder="Filtro ' + title + '" />');

         $('input', this).on('keyup change', function () {

             if (table.column(i).search() !== this.value) {
                 table
                     .column(i)
                     .search(this.value, true, false)

                     .draw();
             }

         });
     });

     $('#tabla_arts tbody').on('click', 'tr', function (e) {
         if ($(e.target).hasClass("ignoreme")) {

         } else {
             $(this).toggleClass('selected');
         }
         //var ordvta = table.rows('.selected').data();
         //var registros = ordvta == null ? 0 : ordvta.length;

         var count = table.rows('.selected').count();
         var $badge = $('#btn_enviar').find('.badge');
         $badge.text(count);

         //console.log(registros);
     });

     $('#btn_enviar').on('click', function (e) {
         e.preventDefault();
         //var oper = $('#btn_enviar').attr('data-operacion');
         click_programar_cambios();

     });

     $('#tabla_arts tbody').on('dblclick', 'tr', function (e) {
         var fila = table.rows(this).data()
         var num = parseFloat(fila[0]['cantidad']).toFixed(4);
         var code = fila[0]['codigo'];
         $('#input_update').val(num)
         $("#ch1").prop("checked", true);
         $("#ch2").prop("checked", false);
         $("#ch3").prop("checked", false);
         $('#updateprogramar').modal('show');


         table.rows().every(function (rowIdx, tableLoop, rowLoop) {
             if (this.data().codigo === code) {
                 var node = this.node();
                 // console.log($(node).hasClass("selected"))
                 if ($(node).hasClass("selected")) {

                 } else {
                     $(node).toggleClass('selected');
                 }
                 var count = table.rows('.selected').count();
                 var $badge = $('#btn_enviar').find('.badge');
                 $badge.text(count);
             }
         });


     })

     function click_programar_cambios() {
         var countOP = table.rows('.selected').count();
         if (countOP == 0) {
             bootbox.dialog({
                 title: "Mensaje",
                 message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                 buttons: {
                     success: {
                         label: "Ok",
                         className: "btn-success m-r-5 m-b-5"
                     }
                 }
             }).find('.modal-content').css({
                 'font-size': '14px'
             });
         } else {
             if (countOP > 1) {
                 $('#input_update').val('')
                
             } else {
                 var fila = table.rows('.selected').data()
                 var num = parseFloat(fila[0]['cantidad']).toFixed(4)
                
                 $('#input_update').val(num)
                
             }
             $("#ch1").prop("checked", true);
             $("#ch2").prop("checked", false);
             $("#ch3").prop("checked", false);
             $('#updateprogramar').modal('show');
         }
     }

     $('#btn_modificacion').on('click', function (e) {
         if ($("#ch1").is(":checked")) {
             if ($('#input_update').val() <= 0 || $('#input_update').val() == '') {
                 bootbox.dialog({
                     title: "Mensaje",
                     message: "<div class='alert alert-danger m-b-0'>Introduzca Cantidad válida.</div>",
                     buttons: {
                         success: {
                             label: "Ok",
                             className: "btn-success m-r-5 m-b-5"
                         }
                     }
                 }).find('.modal-content').css({
                     'font-size': '14px'
                 });
             } else {
                 click_programar(1);
             }
         } else if ($("#ch2").is(":checked")){
             if ($('#input_modificacion').val() < -100 || $('#input_modificacion').val() == '') {
                 bootbox.dialog({
                     title: "Mensaje",
                     message: "<div class='alert alert-danger m-b-0'>Introduzca Porcentaje válido.</div>",
                     buttons: {
                         success: {
                             label: "Ok",
                             className: "btn-success m-r-5 m-b-5"
                         }
                     }
                 }).find('.modal-content').css({
                     'font-size': '14px'
                 });
             } else {
                 click_programar(2);
             }
         } else {
             click_programar(3);
         }
     });
     
     comboboxArticulos_reload();
     $("#sel_articulos").val('').selectpicker('refresh');

     $("#sel_tipomat").on("changed.bs.select", function (e, clickedIndex, newValue, oldValue) {
         e.preventDefault();
         comboboxArticulos_reload();

     });
     $("#sel_articulos").on("changed.bs.select", function (e, clickedIndex, newValue, oldValue) {
         e.preventDefault();
         reload_tabla_arts(true);

     });

     function comboboxArticulos_reload() {
         $.ajax({
             type: 'POST',
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             data: {
                 "_token": tokenapp,
                 tipomat: $("#sel_tipomat option:selected" ).text()
             },
             url: routeapp + "mtto_ldm_combobox_articulos",
             beforeSend: function () {
                $.blockUI({
                    baseZ: 2000,
                    message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                    css: {
                        border: 'none',
                        padding: '16px',
                        width: '50%',
                        top: '40%',
                        left: '30%',
                        backgroundColor: '#fefefe',
                        '-webkit-border-radius': '10px',
                        '-moz-border-radius': '10px',
                        opacity: .7,
                        color: '#000000'
                    }
                });
            },
            complete: function () {
                setTimeout($.unblockUI, 1500);


            },
             success: function (data) {
                 console.log(data)
                 options = [];

                 $("#sel_articulos").empty();
                 for (var i = 0; i < data.oitms.length; i++) {
                     options.push('<option value="' + data.oitms[i]['ItemCode'] + '">' +
                         data.oitms[i]['descr'] + '</option>');
                 }
                 if (data.oitms.length <= 0) {

                 } else {
                     $('#sel_articulos').append(options);
                     //$('#sel_articulos option').attr("selected", "selected");
                 }
                 
                 $('#sel_articulos').selectpicker('refresh');

             }
         });
     }

     function click_programar(option) {
         var ordvta = table.rows('.selected').data();
         //var ordvtac = table.rows('.selected').node();
         //console.log(ordvtac[0])
         var ops = '';
         var registros = ordvta == null ? 0 : ordvta.length;
         for (var i = 0; i < registros; i++) {
             if (i == registros - 1) {
                 ops += ordvta[i].codigo_origen + "&" + parseFloat(ordvta[i].cantidad).toFixed(4);
             } else {
                 ops += ordvta[i].codigo_origen + "&" + parseFloat(ordvta[i].cantidad).toFixed(4) + ",";
             }
             //console.log(ordvta[i]);         
         }

         if (registros > 0) {

             $.ajax({
                 type: 'GET',
                 headers: {
                     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                 },
                 data: {
                      "_token": tokenapp,
                     articulos: ops,
                     input_update: $('#input_update').val(),                    
                     input_modificacion: $('#input_modificacion').val(),
                     option: option,
                     codigo: $("#sel_articulos").val()
                 },
                 url: routeapp + "actualizarCantidad_mtto_ldm",
                 beforeSend: function () {
                     $.blockUI({
                         baseZ: 2000,
                         message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                         css: {
                             border: 'none',
                             padding: '16px',
                             width: '50%',
                             top: '40%',
                             left: '30%',
                             backgroundColor: '#fefefe',
                             '-webkit-border-radius': '10px',
                             '-moz-border-radius': '10px',
                             opacity: .7,
                             color: '#000000'
                         }
                     });
                 },
                 complete: function () {
                     setTimeout($.unblockUI, 1500);


                 },
                 success: function (data) {
                     setTimeout(function () {
                         var respuesta = JSON.parse(JSON.stringify(data));
                         console.log(respuesta)
                         if (respuesta.codigo == 302) {
                             window.location = routeapp + "auth/login";

                         }
                     }, 2000);
                     reload_tabla_arts(false);
                     var $badge = $('#btn_enviar').find('.badge');
                     $badge.text('');
                     $('#updateprogramar').modal('hide');
                     $('#input_update').val('');
                     $('#input_modificacion').val('');
                     console.log(data.mensajeErr)
                     if (data.mensajeErr.includes('Error')) {
                         bootbox.dialog({
                             title: "Mensaje",
                             message: "<div class='alert alert-danger m-b-0'>" + data.mensajeErr + "</div>",
                             buttons: {
                                 success: {
                                     label: "Ok",
                                     className: "btn-success m-r-5 m-b-5"
                                 }
                             }
                         }).find('.modal-content').css({
                             'font-size': '14px'
                         });
                     } else {
                        bootbox.dialog({
                            title: "Mensaje",
                            message: "<div class='alert alert-success m-b-0'>Los cambios se realizarán en breve...</div>",
                            buttons: {
                                success: {
                                    label: "Ok",
                                    className: "btn-success m-r-5 m-b-5"
                                }
                            }
                        }).find('.modal-content').css({
                            'font-size': '14px'
                        });   
                     }
                 }
             });

         } else {
             bootbox.dialog({
                 title: "Mensaje",
                 message: "<div class='alert alert-danger m-b-0'>No hay registros seleccionados.</div>",
                 buttons: {
                     success: {
                         label: "Ok",
                         className: "btn-success m-r-5 m-b-5"
                     }
                 }
             }).find('.modal-content').css({
                 'font-size': '14px'
             });
         }
     }

     function reload_tabla_arts(asyncc) {
         $("#tabla_arts").DataTable().clear().draw();
         $.ajax({
             type: 'GET',
             async: asyncc,
             url: routeapp + "datatables_mtto_ldm",
             data: {
                 codigo: $('#sel_articulos').val()
             },
             beforeSend: function () {
                 if (asyncc) {
                     $.blockUI({
                         baseZ: 2000,
                         message: '<h1>Su petición esta siendo procesada,</h1><h3>por favor espere un momento...<i class="fa fa-spin fa-spinner"></i></h3>',
                         css: {
                             border: 'none',
                             padding: '16px',
                             width: '50%',
                             top: '40%',
                             left: '30%',
                             backgroundColor: '#fefefe',
                             '-webkit-border-radius': '10px',
                             '-moz-border-radius': '10px',
                             opacity: .7,
                             color: '#000000'
                         }
                     });
                 }

             },
             complete: function () {
                 if (asyncc) {
                     setTimeout($.unblockUI, 1500);
                 }

             },
             success: function (data) {

                 if (data.arts.length > 0) {
                     $("#tabla_arts").dataTable().fnAddData(data.arts);
                 } else {

                 }
             }
         });
     }

     function number_format(number, decimals, dec_point, thousands_sep) {
         var n = !isFinite(+number) ? 0 : +number,
             prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
             sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
             dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
             toFixedFix = function (n, prec) {
                 // Fix for IE parseFloat(0.55).toFixed(0) = 0;
                 var k = Math.pow(10, prec);
                 return Math.round(n * k) / k;
             },
             s = (prec ? toFixedFix(n, prec) : Math.round(n)).toString().split('.');
         if (s[0].length > 3) {
             s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
         }
         if ((s[1] || '').length < prec) {
             s[1] = s[1] || '';
             s[1] += new Array(prec - s[1].length + 1).join('0');
         }
         return s.join(dec);
     }
     //});//fin on load

 } //fin js_iniciador               
 function val_btn(val) {

     $('#btn_enviar').attr('data-operacion', val);
 }