/*
this file contain all type of charts represantations, being used on channels for dbee

@ Start date 3 Apr 2013


*/

function dbeeUserActivity(monthData,regiData, actUser)
{

    $(function () {

        var count = 0;
        var totlen = 2;

        $('#activitycontainer').highcharts({
            chart: {
                type: 'line',
                marginRight: 60,
                marginBottom: 50
            },
            title: {
                text: 'monthly platform signup and activity',
                x: -20 //center
            },
    /*            subtitle: {
                    text: 'Source: dbee.me',
                    x: -20
                },
    */            xAxis: {
                categories: monthData,
                labels: {
                    rotation: -25,
                    align: 'right',
                    style: {
                        fontSize: '9px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                   
                }
            },
            yAxis: {
                title: {
                    text: 'user/activity count'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                valueSuffix: ' '
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                x: -10,
                y: 13,
                borderWidth: 0
            },
            plotOptions: {
            series: {
                events: {
                    legendItemClick: function () {
                      
                        if(this.visible== false){
                           this.chart.yAxis[0].setTitle({
                                    text: "user/activity count"
                            });
                            count--;
                        }    
                        if((count+1)==totlen)
                            return false;
                        

                        if(this.visible== true){
                           if(this.name=='Total number of actions')
                           {
                                this.chart.yAxis[0].setTitle({
                                        text: "users count"
                                });
                           } else {
                                this.chart.yAxis[0].setTitle({
                                        text: "activity count"
                                });
                           }

                            count++;
                        }

                        
                     }
                },    
               showInLegend: true
            }
        },
            series: [{
                name: 'user signups on plateform',
                data: regiData
            }, {
                name: 'Total number of actions',
                data: actUser
            }, ]
        });
    });
    


}

function chartofdbeetypes(cdata,divid)
{
   // console.log(cdata);
    var divid = typeof(divid)!='undefined' ? divid : 'piecontainer';
    var chart;
	var cdata	=	cdata;

    var count = 0;

    var totlen = (cdata.length);

    $(document).ready(function () {
    	
    	// Build the chart
        $('#'+divid).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: ' "User created" posting breakdown'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1,
				formatter: function() {
                 	var y = this.y;
					var p = Math.round(this.percentage*100)/100;
					if(this.point.name=='Text')
						return '' + y + ' text posts  (' + p.toFixed(2) + '%)';
                    else if(this.point.name=='Picture')
                        return ' ' + y + ' posts featuring images (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Link')
						return ' ' + y + ' posts featuring URLs (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Video')
						return ' ' + y + ' posts featuring videos (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Polls')
						return ' ' + y + ' voting polls (' + p.toFixed(2) + '%)';
                    else 
                        return ' ' + y + ' '+this.point.name+' (' + p.toFixed(2) + '%)';
				}
            },
			legend:{
			  "layout":"horizontal",
			  "style":{
				 "left":"auto",
				 "bottom":"auto",
				 "right":"auto",
				 "top":"auto"
			  }
		   },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    },
    				point: {
                        events: {
                            legendItemClick: function () {
                               
                                if(this.visible== false)
                                    count--;

                                if((count+1)==totlen)
                                    return false;
                                

                                if(this.visible== true)
                                    count++;

                                //alert(count);
                             }
                        }    
                    },
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'dbee Ratio',
				point: {
				events: {
					click: function(e) {
						
						//console.log(e);
						//location.href = e.point.url;
						//e.preventDefault();
						}
					}
				},
                data: cdata	
            }]
        });
    });
}

function chartofgrops(cdata,divid)
{
    var divid = typeof(divid)!='undefined' ? divid : 'piecontainer';

    var chart;
    var cdata	=	cdata;

    var count = 0;
    var totlen = (cdata.length);

    $(document).ready(function () {
    	
    	// Build the chart
        $('#'+divid).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: ' ‘User created’ group breakdown'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1,
				formatter: function() {
					var y = this.y;
					var p = Math.round(this.percentage*100)/100;
					if(this.point.name=='Open')
						return ' ' + y + ' open groups (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Closed')
						return ' ' + y + ' closed groups (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Request')
						return ' ' + y + ' request groups (' + p.toFixed(2) + '%)';
				}
            },
			legend:{
			  "layout":"horizontal",
			  "style":{
				 "left":"auto",
				 "bottom":"auto",
				 "right":"auto",
				 "top":"auto"
			  }
		   },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    },
                    point: {
                        events: {
                            legendItemClick: function () {
                               
                                if(this.visible== false)
                                    count--;

                                if((count+1)==totlen)
                                    return false;
                                

                                if(this.visible== true)
                                    count++;

                                //alert(count);
                             }
                        }    
                    },
					
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'dbee Ratio',
				point: {
				events: {
					click: function(e) {
						//alert(this.name);
						//console.log(e);
						//location.href = e.point.url;
						//e.preventDefault();
						}
					}
				},
                data: cdata
            }]
        });
    });
}

//<!--pie charts to represent Scores functionality-->
function chartofscores(cdata,divid)
{
    var divid = typeof(divid)!='undefined' ? divid : 'piecontainer';
    var chart;
    var cdata	=	cdata;

    var count = 0;
    var totlen = (cdata.length);

    $(document).ready(function () {
    	
    	// Build the chart
        $('#'+divid).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Plaform scoring'
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1,
				formatter: function() {
					var y = this.y;
					var p = Math.round(this.percentage*100)/100;
					if(this.point.name=='Like')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users have scored Like (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Love')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users have scored Love (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Food For Thought')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users have scored Food For Thought (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Dislike')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users have scored Dislike (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Hate')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users have scored Hate (' + p.toFixed(2) + '%)';
                    else
                        return '<b>'+ this.point.name +'</b>: ' + y + ' users are '+ this.point.name.toLowerCase() +' (' + p.toFixed(2) + '%)';
				}
            },
			legend:{
			  "layout":"horizontal",
			  "style":{
				 "left":"auto",
				 "bottom":"auto",
				 "right":"auto",
				 "top":"auto"
			  }
		   },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    },
                point: {
                        events: {
                            legendItemClick: function () {
                               
                                if(this.visible== false)
                                    count--;

                                if((count+1)==totlen)
                                    return false;
                                

                                if(this.visible== true)
                                    count++;

                                //alert(count);
                             }
                        }    
                    },    
					
                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'dbee Ratio',
				point: {
				events: {
					click: function(e) {
						//alert(this.name);
						//console.log(e);
						//location.href = e.point.url;
						//e.preventDefault();
						}
					}
				},
                data: cdata
            }]
        });
    });
}


function chartofdbees(cdata,id,heading)
{
    var chart;
	var cdata	=	cdata;

    var count = 0;
    var totlen = (cdata.length);
	
    $(document).ready(function () {
    	
    	// Build the chart
        $('#'+id).highcharts({
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: heading
            },
            tooltip: {
        	    pointFormat: '{series.name}: <b>{point.percentage}%</b>',
            	percentageDecimals: 1,
				formatter: function() {

					var y = this.y;
					var p = Math.round(this.percentage*100)/100;
					if(this.point.name=='Posts')
						return ' ' + y + ' posts (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Scores')
						return ' ' + y + ' scores  (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Groups')
						return ' ' + y + ' groups  (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Comments')
						return ' ' + y + ' comments (' + p.toFixed(2) + '%)';
					else if(this.point.name=='Facebook sign in' || this.point.name=='Twitter sign in' || this.point.name=='LinkedIn sign in' || this.point.name=='Google+ sign in' || this.point.name=='Platform registration')
						return '<b>'+ this.point.name +'</b>: ' + y + ' users signed up via '+this.point.name.replace('sign in','')+' (' + p.toFixed(2) + '%)';
                     else 
                    return ' ' + y + ' '+ this.point.name +'  (' + p.toFixed(2) + '%)';
				}
            },
			legend:{
			  "layout":"horizontal",
			  "style":{
				 "left":"auto",
				 "bottom":"auto",
				 "right":"auto",
				 "top":"auto"
			  }
		   },
            plotOptions: {
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: true,
                        color: '#000000',
                        connectorColor: '#000000',
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    },
					point: {
                        events: {
                            legendItemClick: function () {
                               
                                if(this.visible== false)
                                    count--;

                                if((count+1)==totlen)
                                    return false;
                                

                                if(this.visible== true)
                                    count++;

                                //alert(count);
                             }
                        }    
                    },
                   

                    showInLegend: true
                }
            },
            series: [{
                type: 'pie',
                name: 'dbee Ratio',
				point: {
				events: {
					click: function(e) {
						//alert(this.name);
						//console.log(e);
						//location.href = e.point.url;
						//e.preventDefault();
						}
					}
				},
                data: cdata	
            }]
        });
    });
}

function donuteofdbees(cdata,id,heading)
{

    $(function () {
        var colors = Highcharts.getOptions().colors,
        categories = ['POSTS', 'COMMENTS', 'GROUPS','SCORES'],
        name = 'post brands',
        data = cdata

     
    
    
        // Build the data arrays
        var browserData = [];
        var versionsData = [];
        for (var i = 0; i < data.length; i++) {
    
            // add browser data
            browserData.push({
                name: categories[i],
                y: data[i].y,
                color: data[i].color
            });
    
            // add version data
            for (var j = 0; j < data[i].drilldown.data.length; j++) {
                var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                versionsData.push({
                    name: data[i].drilldown.categories[j],
                    y: data[i].drilldown.data[j],
                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                });
            }
        }
    
        // Create the chart
        $('#'+id).highcharts({
            chart: {
                type: 'pie'
            },
            title: {
                text: 'users total activity on platform '
            },
            yAxis: {
                title: {
                    text: 'Users Activity'
                }
            },
            plotOptions: {
                pie: {
                    shadow: false,
                   /* center: ['50%', '50%']*/                }
            },
            tooltip: {
                valueSuffix: ' '
            },
            series: [{
                name: 'Total',
                data: browserData,
                size: '60%',
                dataLabels: {
                    formatter: function() {
                        return this.y > 5 ? this.point.name : null;
                    },
                    color: 'white',
                    distance: -30
                }
            }, {
                name: 'Total',
                data: versionsData,
                size: '80%',
                innerSize: '60%',
                dataLabels: {
                    formatter: function() {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +''  : null;
                    }
                }
            }]
        });
    });
    

}
