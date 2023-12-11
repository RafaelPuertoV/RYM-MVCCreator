
<h1> {{CLASS.PREFIX}}{{CLASS.NAME}} - List </h1>

<a class="btn btn-primary" href="#" onclick="viewAdd()"> <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add new </a><br>
<hr>

<table id="table-list" class="table table-striped">
   
</table>

<script>
    setPrimaryKeyField('{{CLASS.PRIMARYKEY}}');
    apiGetList('{{CLASS.PREFIX}}{{CLASS.NAME}}','table-list');
</script>