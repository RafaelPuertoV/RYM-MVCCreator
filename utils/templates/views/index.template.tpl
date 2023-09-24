
<h1> {{CLASS.PREFIX}}{{CLASS.NAME}} - List </h1>

<table id="table-list" class="table table-striped">
   
</table>

<script>
    setPrimaryKeyField('{{CLASS.PRIMARYKEY}}');
    apiGetList('{{CLASS.PREFIX}}{{CLASS.NAME}}','table-list');
</script>