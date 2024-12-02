<?if (strpos($_SERVER['REQUEST_URI'], '/ajax/')==0):?>

<script>
		
   function js_ajax_post(url,data) {	   
        return $.ajax({
		type: 'POST',
 		data: data,		
		url: "/account/ajax/"+url,
		async: true,
        cache: false,
        contentType: false,
        processData: false});}
   
   /*   
   
   function arr(obj) {
        var rs = new FormData();
        $.each( obj, function( key, value ){rs.append(key,value);});		
        return rs;}
		
   */	
   
   /*
   	function js_dt_reload(name,num){
	
	   ru =  { "sLengthMenu": "_MENU_ на страницу",
				"sSearch": "Фильтр:",
				"sInfo":"Показано _START_ до _END_ из _TOTAL_ записей",
				"sInfoEmpty": "Нет записей",
				"sEmptyTable": "Извините - ничего не найдено",
                "oPaginate": {
                    "sPrevious": "Пред.",
                    "sNext": "След."}};
			
	   uz =  { "sLengthMenu": "_MENU_ қаторли",
				"sSearch": "Филтр:",
				"sInfo":"Курсатилган ёзувлар _START_ дан _END_ гача (умумий ёзувлар сони  _TOTAL_ та)",
				"sInfoEmpty": "Ёзувлар мавжуд эмас",
                "oPaginate": {
                    "sPrevious": "Олдинги",
                    "sNext": "Кейинги"}};

	 oz = {    "sLengthMenu": "_MENU_ qatorli",
				"sInfo":"Ko`rsatilgan yozuvlar _START_ dan _END_ gacha (umumiy yozuvlar soni _TOTAL_ ta)",	
				"sInfoEmpty": "Yozuvlar majvud emas",				
				"sSearch": "Filtr:",
                "oPaginate": {
                    "sPrevious": "Oldingi",
                    "sNext": "Keyingi"}};	
	 
	   kr =  {  "sLengthMenu": "_MENU_ на страницу",
				"sSearch": "Фильтр:",
				"sInfo":"Показано _START_ до _END_ из _TOTAL_ записей",
				"sInfoEmpty": "Нет записей",
				"sEmptyTable": "Извините - ничего не найдено",
                "oPaginate": {
                    "sPrevious": "Пред.",
                    "sNext": "След."}};


		$('.'+name).dataTable({
			"aLengthMenu": [[5, 10, 50, 100, -1], [5, 10, 50, 100, "All"]],
			"iDisplayLength": num,	  
			"bSort": false,			
			"sDom": "<'row'<'col-sm-6'l><'col-sm-6'f>r>t<'center'e><'row'<'col-sm-6'i><'col-sm-6'p>>",
			"sPaginationType": "bootstrap",
			"oLanguage":<?=$lang?>});
	
   	jQuery('.dataTables_wrapper .dataTables_filter input').addClass("form-control"); 
    jQuery('.dataTables_wrapper .dataTables_length select').addClass("form-control");
   }
	*/
   
</script>
<?endif?>