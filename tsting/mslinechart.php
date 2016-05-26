<!DOCTYPE html>
<meta charset="utf-8">
<style>

body {
  font: 11px sans-serif;
}

.axis path,
.axis line {
  fill: none;
  stroke: #000;
  shape-rendering: crispEdges;
}

.x.axis path {
  display: none;
}

.line {
  fill: none;
  stroke: steelblue;
  stroke-width: 1.5px;
}

</style>
<body>
<?php
ini_set('max_execution_time', 600);

include 'connectdb.php';
$mindate = ($conn->query("SELECT min(DATE_TWEET) FROM TWEET;")->fetch_row()[0]);
$maxdate = ($conn->query("SELECT max(DATE_TWEET) FROM TWEET;")->fetch_row()[0]);
$data_text = "date\tnone\tfor\tagainst";

?>

<div id="mslchart"></div>
<br/>
<center>
<input type="date" id="dateDeb" value="<?php echo $mindate; ?>"> Date debut
<input type="date" id="dateFin" value="<?php echo $maxdate; ?>"> Date fin
<br/><br/>
<input type="button" id="update" value="Update" onclick="refreshChart();">
<div id="errors"></div>
</center>
<script src="//d3js.org/d3.v3.min.js"></script>

<?php

$date = date_create($mindate);

for($i=0; $date < date_create($maxdate) ; $i++)
{
date_add($date, date_interval_create_from_date_string('1 days'));
$datet = date_format($date, 'Ymd');

$none = $conn->query("SELECT COUNT(*) FROM TWEET WHERE TOPIC_TWEET = 'None' AND DATE_TWEET = '$datet';")->fetch_row()[0];
$for_eu = $conn->query("SELECT COUNT(*) FROM TWEET WHERE TOPIC_TWEET = 'For_eu' AND DATE_TWEET = '$datet';")->fetch_row()[0];
$against_eu = $conn->query("SELECT COUNT(*) FROM TWEET WHERE TOPIC_TWEET = 'Against_eu' AND DATE_TWEET = '$datet';")->fetch_row()[0];

$data_text .= "\n".$datet."\t".$none."\t".$for_eu."\t".$against_eu;
}

$dfile = fopen("datax.txt", "w") or die("Unable to open file!");
fwrite($dfile, $data_text);


?>
<script>

loadChart("datax.txt");

function refreshChart()
{
document.getElementById("errors").innerHTML = '';

var dated = document.getElementById('dateDeb').value;
var datef = document.getElementById('dateFin').value;
if(dated==''){
	document.getElementById("errors").innerHTML = "date debut est vide";
	return 0;}
var datef = document.getElementById('dateFin').value;
if(datef==''){
	document.getElementById("errors").innerHTML = "date fin est vide";
	return 0;}
if(datef < dated){
	document.getElementById("errors").innerHTML = "date fin doit �tre sup�rieure � date debut";
	return 0;}
sendMslData(dated,datef);
}

function sendMslData(dated,datef){

xmlhttp = new XMLHttpRequest();
xmlhttp.onreadystatechange = function() {
     if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
         loadChart("datax.txt");
     }
};
xmlhttp.open("POST","getMSlineData.php",true);
xmlhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
xmlhttp.send("dd=" + dated + "&df=" + datef);
}

function loadChart(datafile){

document.getElementById("mslchart").innerHTML = "";

var margin = {top: 20, right: 80, bottom: 30, left: 50},
    width = 960 - margin.left - margin.right,
    height = 500 - margin.top - margin.bottom;

var parseDate = d3.time.format("%Y%m%d").parse;

var x = d3.time.scale()
    .range([0, width]);

var y = d3.scale.linear()
    .range([height, 0]);

var color = d3.scale.category10();

var xAxis = d3.svg.axis()
    .scale(x)
    .orient("bottom");

var yAxis = d3.svg.axis()
    .scale(y)
    .orient("left");

var line = d3.svg.line()
    .interpolate("basis")
    .x(function(d) { return x(d.date); })
    .y(function(d) { return y(d.temperature); });

var svg = d3.select("#mslchart").append("svg")
    .attr("width", width + margin.left + margin.right)
    .attr("height", height + margin.top + margin.bottom)
  .append("g")
    .attr("transform", "translate(" + margin.left + "," + margin.top + ")");

d3.tsv(datafile, function(error, data) {
  if (error) throw error;

  color.domain(d3.keys(data[0]).filter(function(key) { return key !== "date"; }));

  data.forEach(function(d) {
    d.date = parseDate(d.date);
  });

  var cities = color.domain().map(function(name) {
    return {
      name: name,
      values: data.map(function(d) {
        return {date: d.date, temperature: +d[name]};
      })
    };
  });

  x.domain(d3.extent(data, function(d) { return d.date; }));

  y.domain([
    d3.min(cities, function(c) { return d3.min(c.values, function(v) { return v.temperature; }); }),
    d3.max(cities, function(c) { return d3.max(c.values, function(v) { return v.temperature; }); })
  ]);
  
  var legend = svg.selectAll('g')
      .data(cities)
      .enter()
      .append('g')
      .attr('class', 'legend');
	  
  legend.append('rect')
      .attr('x', width - 20)
      .attr('y', function(d, i) {
        return i * 20;
      })
      .attr('width', 10)
      .attr('height', 10)
      .style('fill', function(d) {
        return color(d.name);
      });

  legend.append('text')
      .attr('x', width - 8)
      .attr('y', function(d, i) {
        return (i * 20) + 9;
      })
      .text(function(d) {
        return d.name;
      });

  svg.append("g")
      .attr("class", "x axis")
      .attr("transform", "translate(0," + height + ")")
      .call(xAxis);

  svg.append("g")
      .attr("class", "y axis")
      .call(yAxis)
    .append("text")
      .attr("transform", "rotate(-90)")
      .attr("y", 6)
      .attr("dy", ".71em")
      .style("text-anchor", "end")
      .text("Number of tweets");

  var city = svg.selectAll(".city")
      .data(cities)
    .enter().append("g")
      .attr("class", "city");

  city.append("path")
      .attr("class", "line")
      .attr("d", function(d) { return line(d.values); })
      .style("stroke", function(d) { return color(d.name); });

  city.append("text")
      .datum(function(d) { return {name: d.name, value: d.values[d.values.length - 1]}; })
      .attr("transform", function(d) { return "translate(" + x(d.value.date) + "," + y(d.value.temperature) + ")"; })
      .attr("x", 3)
      .attr("dy", ".35em")
      .text(function(d) { return d.name; });	
  
});
}
</script>
<?php 
fclose($dfile);
$conn->close();
//unlink('datax.txt');
?>