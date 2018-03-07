
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
  if (tool == "Triggers") {
    showTriggers();
  }
  if (tool == "Responses") {
    showResponses();
  }
  if (tool == "Users") {
    showUsers();
  }

}

function showDashboard() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  var sel = d3.select("#templateContainer");
  sel.append("span").text("Pull Trigger: ");
  sel.append("button").text("Trigger")
  .on("click", function (e) {
    d3.json("safety.php?go=Trigger&pid=" + user_login_data[3],
        function (data) {
            console.log(data);
        });
  });
  sel.append("br");
  sel.append("span").text("Close Trigger: ");
  sel.append("button").text("Close")
  .on("click", function (e) {
    d3.json("safety.php?go=closeTrigger&pid=" + user_login_data[3],
        function (data) {
            console.log(data);
        });
  });
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
  d3.json("safety.php?go=getDevices&pid=" + user_login_data[3],
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
  sel.append("span").text("Longitude:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "lon");
  sel.append("button").text("Add")
    .on("click", function (e) {
      var lat = $("#lat").val();
      var lon = $("#lon").val();
      d3.json("safety.php?go=addPersonLocation&pid=" + user_login_data[3] +
          "&lat=" + lat + "&lon=" + lon,
          function (data) {
            console.log(data);
          });
    });
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
  sel.append("span").text("Device Type:");
  sel.append("input").attr("type", "text").attr("class", "abox")
    .attr("id", "deviceType");
  sel.append("button").text("Add")
    .on("click", function (e) {
      var did = $("#deviceID").val();
      var dtype = $("#deviceType").val();
      d3.json("safety.php?go=addDevices&did=" + did + "&dtype=" + dtype,
          function (data) {
            console.log(data);
          });
      d3.json("safety.php?go=addDevice&pid=" + user_login_data[3] +
          "&did=" + did,
          function (data) {
            console.log(data);
          });
    });
  d3.json("safety.php?go=getDevices&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
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
            var reftable = d3.select("#adminContent").append("table")
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
            reftbody.selectAll("tr").append("input").attr("class", "abox");
            reftbody.selectAll("tr").append("td").html("&nbsp; + &nbsp;")
              .on("click", function (e) {
                var cell = d3.select(this);
                var obj = d3.select(this.parentNode).data();
                var rel = d3.select(this.parentNode).select("input").property("value");
                console.log(rel);
                d3.text("safety.php?go=addCircle&username=" + obj[0][2]
                    +"&pid=" + user_login_data[3] + "&relationship=" + rel, 
                    function(error, text) {
                      if (error) throw error;
                      console.log(text); // Hello, world!
                    });
              });
          });
    });
  d3.json("safety.php?go=getCircle&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "relationship"];
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

function showTriggers() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getTriggers&pid=" + user_login_data[3],
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
        var color = "white";
        reftbody.selectAll("tr").selectAll("td")
          .filter(function (d, i) { return i == 6; })
          .style("width", "50px").style("text-align", "center")
          .style('background-color', color)
          .on('mouseover', function(){
            d3.select(this).style('background-color', 'green');})
          .on('mouseout', function(){
            d3.select(this).style('background-color', color);})
          .on("click", function (e) {
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            d3.json("safety.php?go=addResponse&tid=" + obj[0][5]
                + "&pid=" + user_login_data[3], 
                function(error, obj) {
                  if (error) throw error;
                  console.log(obj);
                });
          });
      });
}

function showResponses() {
  d3.select("#adminContent").html("");
  d3.select("#templateContainer").html("");
  d3.json("safety.php?go=getResponses&pid=" + user_login_data[3],
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
        var refthead = reftable.append("thead"),
        reftbody = reftable.append("tbody").attr("id", "userList");
        var columns = ["Last name", "First name", "Username", 
        "email", "distance", "TID", "PID", "Status"];
        refthead.append("tr").selectAll("th").data(columns)
          .enter().append("th").attr("align", "left").text(ident);
        reftbody.selectAll("tr").data(data)
          .enter()
          .append("tr").attr("id", function (d, i) { return i; })
          .selectAll("td").data(function (d, i) {
            return d;
          }).enter().append("td").html(ident);
        var color = "white";
        reftbody.selectAll("tr").selectAll("td")
          .filter(function (d, i) { return i == 7; })
          .style("width", "50px").style("text-align", "center")
          .style('background-color', color)
          .on('mouseover', function(){
            d3.select(this).style('background-color', 'green');})
          .on('mouseout', function(){
            d3.select(this).style('background-color', color);})
          .on("click", function (e) {
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            d3.json("safety.php?go=closeResponse&tid=" + obj[0][5]
                + "&pid=" + obj[0][6],
                function(error, obj) {
                  if (error) throw error;
                  console.log(obj);
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
            .attr("method", "post");
            var data = ["LastName", "FirstName", "Email", "Address",
            "City", "Country", "zipcode", "username", "password1",
            "password2"];
            d1.selectAll("span").data(data).enter().append("span")
            .html(function (d) { return d + " &nbsp;";})
            .append("input").attr("class", "abox")
            .attr("name", function (d) { return d;});
            d1.selectAll("span").append("br");
            d1.append("input").attr("type", "hidden")
            .attr("name", "op").attr("value", "signup");
            d1.append("input").attr("type", "hidden")
            .attr("name", "sha1").attr("value", "");
            d1.append("button").text("Submit")
            .on("click", function (e) {
                User.processRegistration();
                return false;
            });
        });
    d3.select("#adminContent").append("div").attr("id", "errorLog");
  }
  d3.json("safety.php?go=getUsers",
      function (data) {
        var reftable = d3.select("#templateContainer").append("table")
          .attr("id", "utable").attr("border", 0);
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
                d3.json("safety.php?go=updateRole&username=" + obj[0][2] +
                    "&role=" + obj[0][4], function (data) {
                    console.log(data);
                });
              }
            });
          });
        if (user_login_data[2] == "admin") {
          refthead.select("tr").append("th").html("delete");
          reftbody.selectAll("tr").append("td").html("X")
          .on("click", function (e) {
            var cell = d3.select(this);
            var obj = d3.select(this.parentNode).data();
            console.log(obj);
            d3.text("auth.php?op=unregister&username=" + obj[0][2], 
                function(error, text) {
                  if (error) throw error;
                  console.log(text); // Hello, world!
                });
          });
        }
      });
}

