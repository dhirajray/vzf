var max_socket_reconnects = 6;
$(function() {
    if(SERVER_NAME=='localhost') socket = io.connect('http://localhost:5000');
    else socket = io.connect('https://secure.onserro.com:5000',{'forceNew':true,
        'max reconnection attempts' : max_socket_reconnects,
        secure: true,'reconnect': true});
    // on connection to server, ask for user's name with an anonymous callback
    socket.on('connect', function(){
        // call the server-side function 'adduser' and send one parameter (value of prompt)
        socket.emit('adduser', SESS_USER_ID,clientID);
        //socket.emit('onlineStatus', SESS2_USER_ID);
    });
    socket.on('chkactivitynotification', function (db,domain_id) { 
        if(adminStatus==true)
            ajaxnotification();
    });

    socket.on('updatevideonoti', function (domain_id) {
        if(domain_id==clientID){
            $.ajax({
            url: BASE_URL + '/admin/index/updatenotify',
            data: {
                'Type': 109
            },
            type: "POST",
            success: function(data) 
            {
                callNotify(109);
            }
        });
        }
    });

    socket.emit('refresh');
    socket.on('onlineStatus', function (data,domain_id) {
    	//console.log(data);
        if(domain_id==clientID)
            $(".onlineCount").html(data);
    }); 
    
    
}); 
function callServer(){
    var all= '<br/>' + name + ' : ' + $('#msg').val(); 
    socket.emit('chat', all); $('#msg').val('');
    return false;
}
