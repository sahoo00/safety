
var globalData = null;

var ident = function (d) { return d; };

function callShow() {
  var tool = $('#select-tools').val();
  d3.select("#templateContainer").html("");
  if (tool == "Dashboard") {
    showDashboard();
  }
  if (tool == "Location") {
    showLocation();
  }
  if (tool == "Devices") {
    showDevices();
  }
  if (tool == "My Safety Circle") {
    showCircle();
  }
  if (tool == "My Triggers") {
    showMyTriggers();
  }
  if (tool == "Triggers to Respond") {
    showTriggers();
  }
  if (tool == "Responses") {
    showResponses();
  }
  if (tool == "Users") {
    showUsers();
  }
  if (tool == "Buy") {
    showBuy();
  }
  if (tool == "Make Devices") {
    showMakeDevices();
  }

}

function showStock() {
  d3.select("#mDevices").html("Stock Devices");
  d3.json("safety.php?go=getStock",
      function (data) {
        var reftable = d3.select("#mDevices").append("table")
          .attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody");
        var columns = ["Device ID", "Device Type"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
}

function showMakeDevices() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#adminContent");
  sel.append("span").text("Device Type:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "deviceType");
  sel.append("br");
  sel.append("button").text("Make")
    .on("click", function (e) {
      $('#addRemoveStatus').html("");
      var dtype = $("#deviceType").val();
      d3.json("safety.php?go=makeDevice&dtype=" + dtype,
          function (error, data) {
            if (error) {
              $('#addRemoveStatus').html("Error");
            }
            else if (data.length == 2 && data[0] == 0) {
              $('#addRemoveStatus').html(data[1]);
            }
            else {
              $('#addRemoveStatus').html("Success");
              console.log(data);
              showStock();
            }
          });
    });
  sel.append("br");
  sel.append("span").attr("id", "addRemoveStatus");
  sel.append("br");
  sel.append("div").attr("id", "mDevices");
  showStock();
}

function showPurchasedDevices() {
  d3.select("#bDevices").html("Purchased Devices");
  d3.json("safety.php?go=getDevices&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#bDevices").append("table")
          .attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody");
        var columns = ["Device ID", "Device Type", "Key"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
}

function showBuy() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getPrices",
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        d3.select("#templateContainer").append("div")
          .attr("id", "addRemoveStatus");
        d3.select("#templateContainer").append("div")
          .attr("id", "bDevices");
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Device Type", "Version", "Price", "Stock", "Buy"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").append("td").append("button").text("Buy")
          .on("click", function (e) {
            $('#addRemoveStatus').html("");
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            d3.json("safety.php?go=buyDevice&dtype=" + obj[0][0]
                +"&pid=" + user_login_data[3],
                function(error, data) {
                  globalData = data;
                  if (error) {
                    $('#addRemoveStatus').html("Error");
                  }
                  else if (data.length == 2 && data[0] == 0) {
                    $('#addRemoveStatus').html(data[1]);
                  }
                  else {
                    $('#addRemoveStatus').html("Success");
                    console.log(data);
                    showPurchasedDevices();
                  }
                });
          });
         showPurchasedDevices();
      });
}

function showDashboard() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#templateContainer");
  sel.append("span").text("Pull Trigger: ");
  sel.append("button").text("Trigger")
    .on("click", function (e) {
      $('#addRemoveStatus').html("Status");
      d3.json("safety.php?go=Trigger&pid=" + user_login_data[3],
          function(error, data) {
            globalData = data;
            if (error) {
              $('#addRemoveStatus').html("Error");
            }
            else if (data.length == 2 && data[0] == 0) {
              $('#addRemoveStatus').html(data[1]);
            }
            else {
              $('#addRemoveStatus').html("Success");
              console.log(data);
            }
          });
    });
  sel.append("br");
  sel.append("span").text("Close Trigger: ");
  sel.append("button").text("Close")
    .on("click", function (e) {
      $('#addRemoveStatus').html("Status");
      d3.json("safety.php?go=closeTrigger&pid=" + user_login_data[3],
          function(error, data) {
            globalData = data;
            if (error) {
              $('#addRemoveStatus').html("Error");
            }
            else if (data.length == 2 && data[0] == 0) {
              $('#addRemoveStatus').html(data[1]);
            }
            else {
              $('#addRemoveStatus').html("Success");
              console.log(data);
            }
          });
    });
  sel.append("br");
  sel.append("div").attr("id", "addRemoveStatus");
  sel.append("br");
  sel.append("span").text("Location: ");
  sel.append("div").attr("id", "pLocation");
  d3.json("safety.php?go=getPersonLocation&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#pLocation").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Latitude", "Longitude"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
  sel.append("br");
  sel.append("span").text("Devices: ");
  sel.append("div").attr("id", "pDevices");
  d3.json("safety.php?go=myDevices&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#pDevices").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Device ID", "Device Type"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
}

function showLocation() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#adminContent");
  sel.append("span").text("Latitude:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "lat");
  sel.append("br");
  sel.append("span").text("Longitude:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "lon");
  sel.append("br");
  sel.append("button").text("Add")
    .on("click", function (e) {
      $('#addRemoveStatus').html("Status:");
      var lat = $("#lat").val();
      var lon = $("#lon").val();
      d3.json("safety.php?go=addPersonLocation&pid=" + user_login_data[3] +
          "&lat=" + lat + "&lon=" + lon,
          function (error, data) {
            globalData = data;
            if (error) {
              $('#addRemoveStatus').html("Error");
            }
            else if (data.length == 2 && data[0] == 0) {
              $('#addRemoveStatus').html(data[1]);
            }
            else {
              $('#addRemoveStatus').html("Success");
              console.log(data);
            }
          });
    });
  sel.append("br");
  sel.append("span").text("Status:")
    .attr("id", "addRemoveStatus");
  d3.json("safety.php?go=getPersonLocation&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Latitude", "Longitude"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
}

function showDevices() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#adminContent");
  sel.append("span").text("Device ID:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "deviceID");
  sel.append("br");
  sel.append("span").text("Device Type:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "deviceType");
  sel.append("br");
  sel.append("span").text("Confirmation Key:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "CKey");
  sel.append("br");
  sel.append("button").text("Add")
    .on("click", function (e) {
      $('#addRemoveStatus').html("");
      var did = $("#deviceID").val();
      var dtype = $("#deviceType").val();
      var ckey = $("#CKey").val();
      console.log([did, dtype, ckey]);
      d3.json("safety.php?go=addDevice&pid=" + user_login_data[3] +
          "&did=" + did + "&dtype=" + dtype + "&ckey=" + ckey,
          function (error, data) {
            if (error) {
              $('#addRemoveStatus').html("Error");
            }
            else if (data.length == 2 && data[0] == 0) {
              $('#addRemoveStatus').html(data[1]);
            }
            else {
              $('#addRemoveStatus').html("Success");
              console.log(data);
            }
          });
    });
  sel.append("br");
  sel.append("span").attr("id", "addRemoveStatus");
  d3.json("safety.php?go=myDevices&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Device ID", "Device Type", "Remove"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").append("td").append("button").text("Remove")
          .on("click", function (e) {
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            var m = new Modal({
              id: 'myRemove',
              header: 'Remove Devices',
            });
            m.getBody().html('<div id="removeDevicesDialog"></div>');
            m.show();
            $('#myRemove').on('shown.bs.modal',function() {
              var sel = d3.select("#removeDevicesDialog");
              sel.append("span").text("Confirmation Key:");
              sel.append("input").attr("type", "text").attr("class", "abox")
                .attr("id", "iCkeyInfo");
              sel.append("br");
              sel.append("button").text("Submit")
                .on("click", function (e) {
                  var ckey = d3.select("#iCkeyInfo").property('value');
                  d3.json("safety.php?go=removeDevice&dtype=" + obj[0][1]
                      +"&pid=" + user_login_data[3] + "&did=" + obj[0][0]
                      + "&ckey=" + ckey,
                      function(error, data) {
                        if (error) {
                          $('#iStatus').html("Error!");
                        }
                        else if (data.length == 2 && data[0] == 0) {
                          $('#iStatus').html(data[1]);
                        }
                        else {
                          $('#iStatus').html("Success");
                          console.log(data);
                        }
                      });
                });
              sel.append("br");
              sel.append("span").text(" Status: ")
                .attr("id", "iStatus");
            });
          });
      });
}

function showCircle() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#adminContent");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "searchText");
  sel.append("button").text("Search")
    .on("click", function (e) {
      var str = $("#searchText").val();
      d3.json("safety.php?go=searchCircle&pid=" + user_login_data[3] +
          "&search=" + str,
          function (data) {
            d3.select("#searchCircleResult").html("");
            var reftable = d3.select("#searchCircleResult").append("table")
              .attr("id", "utable").attr("border", 0);
            var refthead = reftable.append("thead"),
            reftbody = reftable.append("tbody").attr("id", "userList");
            var columns = ["Last name", "First name", "Username", 
            "email", "relationship", "Add"];
            refthead.append("tr").selectAll("th").data(columns)
              .enter().append("th").attr("align", "left").text(ident);
            reftbody.selectAll("tr").data(data)
              .enter()
              .append("tr").attr("id", function (d, i) { return i; })
              .selectAll("td").data(function (d, i) {
                return d;
              }).enter().append("td").html(ident);
            reftbody.selectAll("tr").selectAll("td")
              .filter(function (d, i) { return i == 4; })
              .html("")
              .append("input").attr("class", "abox")
              .attr("value", ident);
            reftbody.selectAll("tr").append("td")
              .append("button").text("Add")
              .on("click", function (e) {
                $('#addRemoveStatus').html("Status");
                globalData = this;
                var cell = d3.select(this);
                var obj = d3.select(this).data();
                var box = d3.select(this.parentNode.parentNode).select("input");
                var rel = box.property("value");
                console.log(rel);
                d3.json("safety.php?go=addCircle&username=" + obj[0][2]
                    +"&pid=" + user_login_data[3] + "&relationship=" + rel, 
                    function(error, data) {
                      if (error) {
                        $('#addRemoveStatus').html("Error");
                      }
                      else if (data.length == 2 && data[0] == 0) {
                        $('#addRemoveStatus').html(data[1]);
                      }
                      else {
                        $('#addRemoveStatus').html("Success");
                        console.log(data);
                      }
                    });
              });
          });
    });
  sel.append("div").attr("id", "searchCircleResult");
  sel.append("div").attr("id", "addRemoveStatus");
  d3.json("safety.php?go=getCircle&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "relationship", "Remove"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").append("td")
          .append("button").text("Remove")
          .on("click", function (e) {
            $('#addRemoveStatus').html("Status");
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj[0][2] + " " + obj[0][4]);
            d3.json("safety.php?go=removeCircle&username=" + obj[0][2]
                +"&pid=" + user_login_data[3] + "&relationship=" + obj[0][4], 
                function(error, data) {
                  if (error) {
                    $('#addRemoveStatus').html("Error");
                  }
                  else if (data.length == 2 && data[0] == 0) {
                    $('#addRemoveStatus').html(data[1]);
                  }
                  else {
                    $('#addRemoveStatus').html("Success");
                    console.log(data);
                  }
                });
          });
      });
}

function showTriggers() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getTriggers&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        d3.select("#templateContainer").append("div")
          .attr("id", "addRemoveStatus");
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "distance", "TID", "Response", "Add"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").append("td")
          .append("button").text("Add")
          .on("click", function (e) {
            var cell = d3.select(this);
            var obj = d3.select(this).data();
            console.log(obj);
            d3.json("safety.php?go=addResponse&tid=" + obj[0][5]
                + "&pid=" + user_login_data[3], 
                function(error, data) {
                  if (error) {
                    $('#addRemoveStatus').html("Error");
                  }
                  else if (data.length == 2 && data[0] == 0) {
                    $('#addRemoveStatus').html(data[1]);
                  }
                  else {
                    $('#addRemoveStatus').html("Success");
                    console.log(data);
                  }
                });
          });
      });
}

function showMyTriggers() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getMyTriggers&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "distance", "TID", "Response"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
      });
}

function showResponses() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getResponses&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        d3.select("#templateContainer").append("div")
          .attr("id", "addRemoveStatus");
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "distance", "TID", "PID", "Status", "Close"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").append("td")
          .append("button").text("Close")
          .on("click", function (e) {
            $('#addRemoveStatus').html("Status:");
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            d3.json("safety.php?go=closeResponse&tid=" + obj[0][5]
                + "&pid=" + obj[0][6],
                function(error, data) {
                  if (error) {
                    $('#addRemoveStatus').html("Error");
                  }
                  else if (data.length == 2 && data[0] == 0) {
                    $('#addRemoveStatus').html(data[1]);
                  }
                  else {
                    $('#addRemoveStatus').html("Success");
                    console.log(data);
                  }
                });
          });
      });
}

function showUsers() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  if (user_login_data[2] == "admin") {
    d3.select("#adminContent").append("div").attr("id", "userForm");
    d3.select("#adminContent").append("button").text("Add User")
        .on("click", function (e) {
            var sel = d3.select("#userForm");
            var d1 = sel.append("div").attr("id", "signup")
            .append("form").attr("action", "auth.php")
            .attr("method", "post").attr("onsubmit", "return false;");
            var data = ["LastName", "FirstName", "Email", "Address", 
            "City", "Country", "State", "zipcode", "username"];
            d1.selectAll("span").data(data).enter().append("span")
            .html(function (d) { return d + " &nbsp;";})
            .append("input").attr("class", "abox")
            .attr("name", function (d) { return d;});
            d1.append("span").html("password1 &nbsp;")
              .append("input")
              .attr("class", "abox")
              .attr("type", "password")
              .attr("name", "password1");
            d1.append("span").html("password2 &nbsp;")
              .append("input")
              .attr("class", "abox")
              .attr("type", "password")
              .attr("name", "password2");
            d1.selectAll("span").append("br");
            d1.append("input").attr("type", "hidden")
            .attr("name", "op").attr("value", "signup");
            d1.append("input").attr("type", "hidden")
            .attr("name", "sha1").attr("value", "");
            d1.append("input").attr("type", "hidden")
            .attr("name", "type").attr("value", "json");
            d1.append("button").text("Submit")
            .on("click", function (e) {
                User.processRegistrationUpdate();
            }); 
        });
    d3.select("#adminContent").append("div").attr("id", "errorLog");
  }
  d3.json("safety.php?go=getUsers",
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        d3.select("#templateContainer").append("div")
          .attr("id", "addRemoveStatus");
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "role", "active"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            res = d.slice(0, 5);
            active = d[5];
            t = d[6];
            if (active == "true") {
              if (t < 60) { active = t + "s"; }
              else if (t < 3600) { active = (t/60).toFixed(2) + " min"; }
              else if (t < 3600 * 24) { active = (t/3600).toFixed(2) + " hrs"; }
              else { active = (t/3600/24).toFixed(2) + " days"; }
            }
            res.push(active);
            return res;
          }).enter().append("td").html(ident);
        reftbody.selectAll("tr").selectAll("td")
          .filter(function (d, i) { return i == 4; })
          .each(function (d) {
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            $(cell.node()).editable({
              type: 'select',
              value: d,
              source: [
              {value: "admin", text: "admin"},
              {value: "verified", text: "verified"},
              {value: "user", text: "user"}
              ],
              success: function(response, newValue) {
                obj[0][4] = newValue;
                $('#addRemoveStatus').html("Status:");
                d3.json("safety.php?go=updateRole&username=" + obj[0][2] +
                    "&role=" + obj[0][4],
                    function(error, data) {
                      if (error) {
                        $('#addRemoveStatus').html("Error");
                      }
                      else if (data.length == 2 && data[0] == 0) {
                        $('#addRemoveStatus').html(data[1]);
                      }
                      else {
                        $('#addRemoveStatus').html("Success");
                        console.log(data);
                      }
                    });
              }
            });
          });
        if (user_login_data[2] == "admin") {
          refthead.select("tr").append("th").html("delete");
          reftbody.selectAll("tr").append("td")
            .append("button").text("Delete")
            .on("click", function (e) {
              $('#addRemoveStatus').html("Status:");
              var cell = d3.select(this);
              var obj = d3.select(this.parentNode).data();
              console.log(obj);
              d3.json("auth.php?op=unregister&username=" + obj[0][2] +
                  "&type=json", 
                  function(error, data) {
                    console.log(data); // Hello, world!
                    if (error) {
                      $('#addRemoveStatus').html("Error");
                    }
                    else {
                      $('#addRemoveStatus').html("Success");
                    }
                  });
            });
        }
      });
}

