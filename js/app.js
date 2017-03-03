$(document).ready(function(){
    $.material.init();


    // Initialize Firebase
    var config = {
        apiKey: "AIzaSyBg15duzLJjQYTpJ35JzdAE1FU8M-14EkM",
        authDomain: "puri-contact.firebaseapp.com",
        databaseURL: "https://puri-contact.firebaseio.com",
        storageBucket: "puri-contact.appspot.com",
        messagingSenderId: "139827698824"
    };
    firebase.initializeApp(config);

    // Get a reference to the database service
    var database = firebase.database();

    // Get elements
    const preObject = document.getElementById('object');
    const ulList = document.getElementById('list');
    const sendto_firebase = document.getElementById('sendto_firebase');

    // Create references
    const dbRefObj = firebase.database().ref().child('object');
    const dbRefList = dbRefObj.child('Line_contact');

    // sync object
    //dbRefObj.on('value',snap => console.log(snap.val()));
    dbRefObj.on('value',snap => {
        preObject.innerText = JSON.stringify(snap.val(),null,3);
    });

    //dbRefList.on('child_added',snap => console.log(snap.val()));
    // เมื่อมีการเพิ่ม child
    dbRefList.on('child_added',snap =>{
        const li = document.createElement('li');
        //li.innerText = snap.val();
        //li.id = snap.key;
        console.log(snap.val());
        $('#sendto_firebase').append("<option value='"+555+"'>"+snap.val()+"</option>");
    });
    // เมื่อมีการแก้ไข child
    dbRefList.on('child_changed',snap => {
        const liChanged = document.getElementById(snap.key);
        liChanged.innerText = snap.val();
    });
    // เมื่อมีการลบ child
    dbRefList.on('child_removed',snap => {
        const liRemoved = document.getElementById(snap.key);
        liRemoved.remove();
    });



    var mongo_apikey = 'JrLs9PiSVp8OfgZn_jbSdKCvO01BIbxx';
    var url = 'https://api.mlab.com/api/1/databases/puridb/collections/col_line_id?apiKey='+mongo_apikey;
    $.get(url,function(response){
        //var res = $.parseJSON(response);
        //console.log(response);
        $.each(response,function(i,v){
            //console.log(i);
            //console.log(v);
            //console.log(v.line_id);
            $("#sendto").append("<option value='"+v.line_id+"'>"+v.nickname+"</option");
        });
    });


    $("body").on("submit","#form_sender",function(e){
        e.preventDefault();
        var this_ = $(this);
        var url = this_.attr('action');
        var data = this_.serialize();
        $.post(url,data,function(response){
            var res = $.parseJSON(response);
            if(res.success){
                toastr.success(res.feedback);
                this_.find('select,input').val('');
            }else{
                toastr.danger(res.feedback);
            }
        })
    });
});