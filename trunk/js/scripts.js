function sortTable (column, id){
	var tbl = document.getElementById(id).tBodies[0];
	var store = [];
	for(var i=0, len=tbl.rows.length; i<len; i++){
	    var row = tbl.rows[i];
	    var sortnr = parseFloat(row.cells[column].textContent || row.cells[column].innerText);
	    if(!isNaN(sortnr)) store.push([sortnr, row]);
	}
	store.sort(function(x,y){
	    return x[0] - y[0];
	});
	for(var i=0, len=store.length; i<len; i++){
	    tbl.appendChild(store[i][1]);
	}
	store = null;
};

