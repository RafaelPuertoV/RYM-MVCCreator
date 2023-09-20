<link rel="stylesheet" href="./css/style.css">

<h1> {{CLASS.NAME}} - List </h1>

<table id="table-list" class="table table-striped">
   
</table>

<script>
    setPrimaryKeyField('{{CLASS.PRIMARYKEY}}');
    apiGetList('{{CLASS.NAME}}','table-list');
</script>