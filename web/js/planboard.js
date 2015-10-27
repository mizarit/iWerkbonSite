var list_data = null;
var appointment_list = null;
var Planboard = null;
var from_customer_id = null;
var start_method = null;

Event.observe(window, 'load', function() {
    var PlanboardObject = Class.create(AdminBase, {
        current_row: null,
        current_data: null,
        col_width: 0,
        col_height: 0,
        map: null,
        apps: [],
        date: null,
        was_dragged: false,
        hasDatePicker: false,
        resource_map: [],
        scheduledApps: [],
        unscheduledApps: [],
        pinImages: {},
        markers: {},
        directionsService: null,
        directionsDisplay: null,
        settings: {
            dataURL: planboard_data_url
        },

        initialize: function (config) {
        },

        render: function()
        {
            this.calculateDimensions();
            this.gridInit();
            this.mapInit();
            this.plotCompany();

            d = new Date();
            dd = d.getDate();
            mm = d.getMonth() + 1;
            yy = d.getFullYear();
            if (dd<10) dd = '0'+dd;
            if (mm<10) mm = '0'+mm;
            date = dd+'-'+mm+'-'+yy;

            this.plotAppointments(date);

            this.initUnscheduled();
            this.initDirections();
            this.initUnschedule();

            Event.observe($('appointment-add-link'), 'click', Planboard.new);

            if(from_customer_id) {
                Planboard.newForCustomer(from_customer_id);
                from_customer_id = null;
            }
            if(start_method) {
                switch(start_method){
                    case 'new':
                        Planboard.new();
                }
            }

            Planboard.update();
        },

        update: function(force)
        {
            //list_data = JSON.parse(localStorage.getItem('list_data_'+this.element));
            list_data_expires = localStorage.getItem('expire_planboard');
            now = Date.now() / 1000 | 0;
            if (force || list_data_expires < (now - 15)) { // 15 seconds
                console.log('updating...');
                new Ajax.Request('/admin/planboardAjax', {
                    parameters: {
                        method: 'planboard'
                    },
                    onSuccess: function(transport) {
                        localStorage.setItem('list_data_planboard', Object.toJSON(transport.responseJSON));
                        Planboard.setAppointments(transport.responseJSON);
                        Planboard.plotAppointments(Planboard.date);
                    }
                });
                now = Date.now() / 1000 | 0;
                localStorage.setItem('expire_planboard', now);
            }
            setTimeout(Planboard.update, 1000);
        },

        gridInit: function()
        {
          $$('#planning ul li ul li').each(function(s, i) {
              Event.observe(s, 'click', Planboard.clickGrid);
          });

            $$('#resources ul li ul li').each(function(s, i) {
                Event.observe(s, 'click', Planboard.clickResource);
            });
        },
        clickGrid: function(event) {
            parts = this.id.split('_');
            if (parts[0] == 'resource') {
                team = parts[1];
                resource = parts[2];

                offset = 20;

                x = event.layerX - offset;

                delta = x / Planboard.col_width;
                m = Math.round(delta * 60);
                h = Math.floor(m / 60);
                m = m - (h*60);
                h = h + 8; // add start hours of the grid

                m = (Math.round(m / 5) * 5); // grid align on 5 minute marks
                if (m < 10) m = '0'+m;
                t = h+':'+m;
                Planboard.new(resource, Planboard.date, t);

            }
        },
        clickResource: function(event) {
            isActive = this.hasClassName('active');
            $$('#resources ul li ul li').each(function(s,i) {
                $(s).removeClassName('active');
            });
            if (isActive) {
                var waypts = [];
                Planboard.directionsDisplay.setMap(null);
            }
            else {
                this.addClassName('active');
                Planboard.directionsDisplay.setMap(Planboard.map);

                resource = this.id.substr(9);
                var resource_id = resource_map[resource];
                date = Planboard.date;

                var waypts = [];

                if (Planboard.apps[date]) {
                    $(Planboard.apps[date]).each(function (s, i) {
                        if (s.resource == resource_id) {
                            //console.log(s);
                            waypts.push({
                                //location:new google.maps.LatLng({lat: s.latitude, lng: s.longitude}),
                                location: {lat: s.latitude, lng: s.longitude},
                                stopover: true
                            });
                        }
                    });
                }


                companyLocation = Planboard.getCompanyLocation();
                var request = {
                    origin: new google.maps.LatLng(companyLocation.lat, companyLocation.lng),
                    destination: new google.maps.LatLng(companyLocation.lat, companyLocation.lng),
                    travelMode: google.maps.TravelMode.DRIVING,
                    waypoints: waypts,
                    optimizeWaypoints: true
                };
                Planboard.directionsService.route(request, function (response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        Planboard.directionsDisplay.setDirections(response);
                    }
                });
            }

        },
        plotAppointments: function(date) {
            // date is in nl notation
            // first, clear all apps on the grid

            this.date = date;

            $$('.workorder').each(function(s) {
                $(s).remove();
            });

            for (i in Planboard.markers) {
                Planboard.markers[i].setMap(null);
            }
            Planboard.markers = {};

            if (this.apps[date]) {
                $(this.apps[date]).each(function(s, i) {
                    Planboard.plotAppointment(s, i);
                });
            }
        },

        calculateDimensions: function() {
            this.col_height = $('resources').getHeight();
            var s = $('planboard').getWidth() - $('resources').getWidth() - 14; // 2x 7px from content-inner.wide
            this.col_width = s / 13;
            $$('#planboard #legenda ul li').each(function(s,i) {
                $(s).setStyle({height:Planboard.col_height+'px', width:Planboard.col_width+'px'});
            });
            $$('#planboard #legenda ul li div').each(function(s,i) {
                $(s).setStyle({height:Planboard.col_height+'px'});
            });

            map_height = ($('content-inner').getHeight()- $('planboard').getHeight()) - 120;
            $('map').setStyle({height:map_height+'px'});
        },

        getCompanyLocation: function() {
            company = this.getCompanyInfo();
            return { lat: company.latitude, lng: company.longitude };
        },

        getCompanyInfo: function() {
            return companyInfo;
        },

        plotCompany: function() {
            var marker = new google.maps.Marker({
                position: Planboard.getCompanyLocation(),
                map: Planboard.map,
                title: Planboard.getCompanyInfo().name
            });
        },

        mapInit: function() {
            var mapOptions = {
                center: Planboard.getCompanyLocation(),
                zoom: 12
            };
            Planboard.map = new google.maps.Map(document.getElementById('map'),
                mapOptions);
        },

        getColorCode: function(color) {
            colors = [];
            colors[0] = 'FFEC94';
            colors[1] = 'FFAEAE';
            colors[2] = 'FFF0AA';
            colors[3] = 'B0E57C';
            colors[4] = 'B4D8E7';
            colors[5] = '56BAEC';
            colors[6] = 'FFEC94';
            colors[7] = 'FFAEAE';
            colors[8] = 'FFF0AA';
            colors[9] = 'B0E57C';
            colors[10] = 'B4D8E7';
            colors[11] = '56BAEC';
            colors[12] = 'FFEC94';
            colors[13] = 'FFAEAE';
            colors[14] = 'FFF0AA';
            colors[15] = 'B0E57C';
            colors[16] = 'B4D8E7';
            colors[17] = '56BAEC';
            colors[18] = 'FFEC94';
            colors[19] = 'FFAEAE';
            colors[20] = 'FFF0AA';
            colors[21] = 'B0E57C';
            colors[22] = 'B4D8E7';
            colors[23] = '56BAEC';

            return colors[color] ? colors[color] : colors[0];
        },

        plotAppointment: function (s, i){
            if ($('scheduled-'+ s.id)) {
                old = $('scheduled-'+ s.id);
                old.remove();
                if (Planboard.markers[s.id]) {
                    marker = Planboard.markers[s.id];
                    marker.setMap(null);
                    delete Planboard.markers[s.id];
                }
            }
            startTime = s.start;
            endTime = s.finish;
            title = s.title;
            customer = s.customer;
            team = s.team;
            resource = Planboard.resource_map.indexOf(s.resource) + 1;
            longitude = s.longitude;
            latitude = s.latitude;
            color = s.color;

            p1 = startTime.split(':');
            t1 = (parseInt(p1[0]) * 60) + parseInt(p1[1]);

            p2 = endTime.split(':');
            t2 = (parseInt(p2[0]) * 60) + parseInt(p2[1]);

            // calculate the width, from the duration
            width = ((t2 - t1) / 60) * Planboard.col_width;

            // calculate the offset, from the starttime
            base_offset = (8*60); // grid starts at 8:00
            m_offset = t1 - base_offset; // offset in minutes from start of grid
            offset = (m_offset / 60) * Planboard.col_width;

            var div = new Element('div');
            div.addClassName('workorder');

            div.id = 'scheduled-'+ s.id;
            div.setStyle({position:'absolute',overflow:'hidden',height:'3.3em',width: width+'px', left: offset+'px', backgroundColor: '#'+Planboard.getColorCode(color)});
            div.innerHTML = '<div><strong style="font-size:0.9em">'+customer+'</strong><br>'+title+'</div>';

            Event.observe(div, 'mouseover', function() {
                marker = Planboard.markers[this.id.substr(10)];
                marker.setAnimation(google.maps.Animation.BOUNCE);
            });
            Event.observe(div, 'mouseout', function() {
                marker = Planboard.markers[this.id.substr(10)];
                marker.setAnimation(null);
            });
            //for (i in Planboard.markers) {
            var team_row = team;
            var resource_row = resource;
            $$('#planning .resource_'+team_row+'_'+resource_row).each(function(s,i) {
                $(s).insert(div);
            });

            duration = t2 - t1;

            //Planboard.scheduledApps[Planboard.date][i].duration = duration;

            appointment = {
                title: title,
                duration: duration,
                customer: customer,
                //address: 'Richard Holstraat 35<br>2324VH Leiden',
                longitude: longitude,
                latitude: latitude,
                id: s.id
            };

            Planboard.scheduledApps[Planboard.scheduledApps.length] = appointment;
            Event.observe(div, 'click', function(event) {
                if (Planboard.was_dragged) {
                    Planboard.was_dragged = false;
                    Event.stop(event);
                    return;
                }
                Planboard.edit(this.id.substr(10));
                Event.stop(event); // to prevent gridclick for new appointment
            });

            new Draggable(div.id, {
                revert: true,
                snap: Planboard.col_width / 12,
                onStart: function(elem) {
                    $(elem.element).addClassName('dragging');
                },
                onEnd: function(elem)
                {
                    $(elem.element).removeClassName('dragging');
                    Planboard.was_dragged = true;
                }
            });

            var pinColor = Planboard.getColorCode(color);

            if (!this.pinImages['c'+pinColor]) {
                this.pinImages['c'+pinColor] = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_letter&chld=%E2%80%A2|" + pinColor,
                    new google.maps.Size(21, 34),
                    new google.maps.Point(0,0),
                    new google.maps.Point(10, 34));
            }

            var pinShadow = new google.maps.MarkerImage("http://chart.apis.google.com/chart?chst=d_map_pin_shadow",
                new google.maps.Size(40, 37),
                new google.maps.Point(0, 0),
                new google.maps.Point(12, 35));

            // now, plot the appointment on the map
            var marker = new google.maps.Marker({
                position: {lat: latitude, lng: longitude},
                map: Planboard.map,
                icon: this.pinImages['c'+pinColor],
                shadow: pinShadow,
                title: customer+"\n"+title
            });

            Planboard.markers[s.id] = marker;
        },
        initUnscheduled:  function ()
        {
            /*
            $$('.appointment').each(function(s, i) {
                new Draggable(s.id, { revert: true,
                    onStart: function(elem) {
                        console.log(elem.element.id);
                        app = Planboard.unscheduledApps[elem.element.id.substr(4)];
                        width = Planboard.col_width * (app.duration / 60);
                        $(elem.element).setStyle({height: '2em', width: width+'px'});
                        $(elem.element).addClassName('dragging');
                    },
                    onEnd: function(elem)
                    {
                        $(elem.element).setStyle({height: 'auto', width: 'auto'});
                        $(elem.element).removeClassName('dragging');
                    }
                });
            });
            */
        },

        addUnscheduled: function(data)
        {
            this.unscheduledApps[this.unscheduledApps.length] = data;
        },

        initDirections: function()
        {
            Planboard.directionsService = new google.maps.DirectionsService();
            Planboard.directionsDisplay = new google.maps.DirectionsRenderer();
        },

        initUnschedule: function()
        {
            /*
            Droppables.add('dropzone', {
                accept: 'workorder',
                hoverclass: 'hovering',
                onDrop: function(element) {
                    id = element.id.substr(10);

                    n = Planboard.unscheduledApps.length;
                    Planboard.unscheduledApps[n] = {
                        title: Planboard.scheduledApps[id].title,
                        duration: Planboard.scheduledApps[id].duration,
                        customer: Planboard.scheduledApps[id].customer,
                        longitude: Planboard.scheduledApps[id].longitude,
                        latitude: Planboard.scheduledApps[id].latitude
                    };

                    var li = new Element('li');
                    var div = new Element('div');
                    li.insert(div);
                    div.id = 'app-'+(n-1);
                    div.addClassName('appointment');
                    div.innerHTML = '<strong>'+Planboard.scheduledApps[id].title+'<br>'+duration+' minuten</strong><br>'+Planboard.scheduledApps[id].customer+'</div>';
                    $('hook').insert({before: li});

                    //$(element).parentNode.hide();

                    Planboard.initUnscheduled();
                }

            });
*/
            $$('#planning ul li ul li').each(function(s,i) {
                Droppables.add(s, {
                    accept: ['appointment','workorder'],
                    onDrop: function(element) {

                        o =  $(element).hasClassName('appointment') ? 4 : 10;

                        parts = ($(s).id.split('_'));
                        team_id = parts[1];
                        var resource_id = parts[2] - 1;
                        x = ($(element).cumulativeOffset()[0]);
                        app = (element.id.substr(o));
                        var app_data = null;
                        if ($(element).hasClassName('appointment')) {
                            app_data = Planboard.unscheduledApps[app];
                        }
                        else {
                            for(i in Planboard.scheduledApps) {
                                if (!Planboard.scheduledApps.hasOwnProperty(i)) continue;
                                if(Planboard.scheduledApps[i].id == app) {
                                    app_data = Planboard.scheduledApps[i];
                                }
                            }
                        }

                        grid_start = $('planning').cumulativeOffset()[0];
                        offset = x - grid_start ;
                        if (offset < 0) offset = 0;

                        // now, offset is relative to 8:00
                        hours_after_start = offset / Planboard.col_width;
                        hours_after_start += 8;

                        // snap to grid
                        hours = Math.floor(hours_after_start);
                        minutes = Math.round(60 * ( hours_after_start - hours), 2);
                        rem = minutes % 5;
                        minutes = minutes - rem;
                        /*
                        if (rem < 3) {
                            minutes = minutes - rem;
                        }
                        else {
                            minutes = minutes + 5;
                            if (minutes >= 60) {
                                hours++;
                                minutes = 0;
                            }
                        }*/

                        var startTime = (hours < 10 ? '0' + hours : hours) + ':' + (minutes < 10 ? '0' + minutes : minutes);
                        sm = (hours * 60) + minutes;
                        sm += app_data.duration;
                        minutes_end = sm % 60;
                        hours_end = (sm - minutes_end) / 60;
                        var endTime = (hours_end < 10 ? '0' + hours_end : hours_end) + ':' + (minutes_end < 10 ? '0' + minutes_end : minutes_end);

                        if (Planboard.apps[Planboard.date]) {
                            $(Planboard.apps[Planboard.date]).each(function(s, i) {
                                if (s.id == app_data.id) {

                                    //Planboard.edit(this.id.substr(10));
                                    if(Planboard.apps[Planboard.date][i].start == startTime) {
                                        // no change, consider this a click
                                        Planboard.edit(s.id);
                                    }
                                    Planboard.apps[Planboard.date][i].start = startTime;
                                    Planboard.apps[Planboard.date][i].finish = endTime;
                                    Planboard.apps[Planboard.date][i].resource = Planboard.resource_map[resource_id];

                                    new Ajax.Request('/admin/planboardData', {
                                        parameters: {
                                            form: 'grid',
                                            method: 'move',
                                            id: s.id,
                                            start: startTime,
                                            finish: endTime,
                                            resource: Planboard.resource_map[resource_id]
                                        },
                                        onSuccess: function (transport) {
                                            //console.log(transport);
                                        }
                                    });
                                    Planboard.plotAppointment(s, i);
                                }
                            });
                        }

                        if ($(element).hasClassName('appointment')) {
                            $(element).parentNode.hide();
                        }
                        else {
                            $(element).hide();
                        }

                        Planboard.was_dragged = false;
                    }
                });
            });
        },

        initFormElements: function()
        {
            //if (!Planboard.hasDatePicker) {
           if($($('appointment-date').parentNode).hasClassName('my-datepicker-container')) {
               // this is not really a fix, it just hides the problem
               elem = $('appointment-date').clone();
               elem.setStyle({width:'6em'});

               Element.replace($('appointment-date').parentNode, elem);
               //$($('appointment-date').parentNode).removeClassName('my-datepicker-container')
           }
                new MY.DatePicker({
                    input: 'appointment-date',
                    format: 'dd-MM-yyyy',
                    showWeek: true
                });
              //  Planboard.hasDatePicker = true;
            //}
            var t1 = new Proto.TimePicker("appointment-time-from", { step: 15, startTime:"8:00", endTime:"20:00"});
            var t2 = new Proto.TimePicker("appointment-time-till", { step: 15, startTime:"8:00", endTime:"20:00"});

            var oldTime = t1.getTime();

            $("appointment-time-from").on('time:change', function() {
                if ($("appointment-time-till").value) { // Only update when second input has a value.
                    var duration = (t2.getTime() - oldTime);
                    var time = t1.getTime();
                    t2.setTime(new Date(new Date(time.getTime() + duration)));
                    oldTime = time;
                }
            });
            $("appointment-time-till").on('time:change', function() {
                if(t1.getTime() > t2.getTime()) {
                    this.addClassName("error");
                }
                else {
                    this.removeClassName("error");
                }
            });

            var ac1 = new AutoComplete($('appointment-zipcode'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=zipcode'});
            var ac2 = new AutoComplete($('appointment-ctitle'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=customer'});
            var ac3 = new AutoComplete($('appointment-title'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=products'});

            $('color-1').setStyle({background:'#'+Planboard.getColorCode(0)});
            $('color-2').setStyle({background:'#'+Planboard.getColorCode(1)});
            $('color-3').setStyle({background:'#'+Planboard.getColorCode(2)});
            $('color-4').setStyle({background:'#'+Planboard.getColorCode(3)});
            $('color-5').setStyle({background:'#'+Planboard.getColorCode(4)});
            $('color-6').setStyle({background:'#'+Planboard.getColorCode(5)});
        },
        new: function(resource, date, time)
        {
            var dataMap = {
                customer: 'appointment-ctitle',
                title: 'appointment-title',
                resource: 'appointment-resource',
                start: 'appointment-time-from',
                finish: 'appointment-time-till',
                date: 'appointment-date',
                remarks: 'appointment-remarks',
                address: 'appointment-address',
                zipcode: 'appointment-zipcode',
                city: 'appointment-city',
                phone: 'appointment-phone',
                email: 'appointment-email',
                color: 'appointment-color',
                customer_id: 'appointment-customer-id'
            };
            $$('.extra-field').each(function(s,i) {
                dataMap['extra_'+s.id.substr(18)] = s.id
            });
            $$('.appointment-checklist').each(function(s,i) {
                dataMap['checklist_'+s.id.substr(10)] = s.id
            });

            var selectedTime = time;
            var selectedDate = date;
            Planboard.renderForm('Afspraak toevoegen', $('appointment-form'), {
                dataURL: '/frontend_dev.php'+Planboard.settings.dataURL,
                dataMap: dataMap,
                customRender: function(data) {
                    $$('#appointment-form input').each(function(s) {
                        if (s.type == 'text') {
                            if (s.id == 'appointment-time-from') {
                                $('appointment-time-from').value = selectedTime ? selectedTime : '8:00';

                            } else if (s.id == 'appointment-time-till') {
                                t = selectedTime ? selectedTime : '8:00';
                                parts = t.split(':');
                                h = parseInt(parts[0]);
                                m = parseInt(parts[1]);
                                if (h==23) {
                                    m = 59;
                                }
                                else {
                                    h++;
                                }
                                $('appointment-time-till').value = h+':'+(m<10?'0'+m:m);
                            } else if (s.id == 'appointment-date') {
                                $(s).value = selectedDate ? selectedDate : Planboard.date;

                            } else {
                                $(s).value = '';
                            }


                        }
                    });

                    Planboard.initFormElements();
                }
            });


            if (resource) {
                // grid was clicked, prefill selected resource, date and time
                $('appointment-resource').options.selectedIndex = resource;
                if (!time) {
                    t = new Date;
                    time = t.getHours()+':'+ t.getMinutes();
                }
                if (!date) {
                    date = Planboard.date;
                }
                parts = time.split(':');
                endTime = (parseInt(parts[0])+1)+':'+parts[1];
                $('appointment-time-from').value = time;
                $('appointment-time-till').value = endTime;
                $('appointment-date').value = date;

                $('appointment-orderrows-title').hide();
                $('delete-app-btn').hide();
            }
        },

        newForCustomer: function(customer) {
            Planboard.renderForm('Afspraak toevoegen', $('appointment-form'), {});

            Planboard.initFormElements();

            new Ajax.Request('/frontend_dev.php/admin/customersData?form=search&method=customer', {
                parameters: {
                    id: customer
                },
                onSuccess: function (transport) {
                    var data = transport.responseJSON[0].data;
                    for (i in data) {
                        if ($(i)) {
                            $(i).value = data[i];
                        }
                    }
                }


            });
        },

        edit: function(appointment)
        {
            Planboard.current_row = appointment;

            var dataMap = {
                customer: 'appointment-ctitle',
                title: 'appointment-title',
                resource: 'appointment-resource',
                start: 'appointment-time-from',
                finish: 'appointment-time-till',
                date: 'appointment-date',
                remarks: 'appointment-remarks',
                address: 'appointment-address',
                zipcode: 'appointment-zipcode',
                city: 'appointment-city',
                phone: 'appointment-phone',
                email: 'appointment-email',
                color: 'appointment-color',
                customer_id: 'appointment-customer-id'
            };
            $$('.extra-field').each(function(s,i) {
                dataMap['extra_'+s.id.substr(18)] = s.id
            });
            $$('.appointment-checklist').each(function(s,i) {
                dataMap['checklist_'+s.id.substr(10)] = s.id
            });

            Planboard.renderForm('Afspraak bewerken', $('appointment-form'), {
                0: appointment,
                dataURL: '/frontend_dev.php/admin/planboardData',
                dataMap: dataMap,
                customRender: function(cdata)
                {
                    $('appointment-orderrows').innerHTML = '';
                    if (cdata.orderrows) {

                        Planboard.current_data = cdata;

                        var table = new Element('table');
                        var thead = new Element('thead');
                        var tr = new Element('tr');
                        tr.insert(new Element('th').update('Aantal').setStyle({width: '220px'}));
                        tr.insert(new Element('th').update('Omschrijving'));
                        tr.insert(new Element('th').update('Acties').setStyle({width: '1.3em'}));
                        table.insert(tr);
                        $('appointment-orderrows').insert(table);

                        for (i in cdata.orderrows) {
                            if (!cdata.orderrows.hasOwnProperty(i)) continue;
                            row = cdata.orderrows[i];

                            //$(cdata.orderrows).each(function(row, i){
                            var tr = new Element('tr');
                            eval("Event.observe(tr, 'click', function() { Planboard.editRow(" + i + "); });");
                            tr.insert(new Element('td').update(row.c));
                            tr.insert(new Element('td').update(row.d));

                            var td = new Element('td').setStyle({textAlign: 'right'});
                            var a = new Element('a');
                            var io = new Element('i');
                            io.addClassName('fa');
                            io.addClassName('fa-edit');
                            eval("Event.observe(a, 'click', function() { Planboard.editRow(" + i + "); });");
                            a.insert(io);
                            td.insert(a);
                            td.insert('&nbsp;');

                            var a = new Element('a');
                            var io = new Element('i');
                            io.addClassName('fa');
                            io.addClassName('fa-remove');
                            eval("Event.observe(a, 'click', function() { Planboard.removeRow(" + i + "); });");
                            a.insert(io);
                            td.insert(a);
                            td.insert('&nbsp;');
                            tr.insert(td);

                            table.insert(tr);
                        // });
                        }
                    }
                    else {
                        $('appointment-orderrows').insert(new Element('p').update('Deze werkbon heeft geen orderregels'));
                    }
                    if (cdata.extra_fields) {
                        for (i in cdata.extra_fields) {
                            if ($('appointment-extra-'+i)) {
                                $('appointment-extra-' + i).value = cdata.extra_fields[i];
                            }
                        }
                    }

                    if (cdata.checklists) {
                        for (i in cdata.checklists) {
                            if ($('checklist-'+i)) {
                                $('checklist-' + i).checked = cdata.checklists[i];
                            }
                        }
                    }

                    $$('.color').each(function(s) {
                        s.removeClassName('active-color');
                    });

                    $('color-'+(cdata.color+1)).addClassName('active-color');

                    $$('.color').each(function(s) {
                        Event.observe(s, 'click', function() {
                            $$('.color').each(function(s) {
                                s.removeClassName('active-color');
                            });
                            this.addClassName('active-color');
                            $('appointment-color').value = parseInt(this.id.substr(6)) - 1;
                        });
                    });
                    Event.observe($('workorder-add-link'), 'click', Planboard.addRow);
                    Event.observe($('delete-app-btn'), 'click', Planboard.remove);

                    $('appointment-orderrows-title').show();
                    $('delete-app-btn').show();
                },
                customSave: function(data)
                {
                    Planboard.update(true);
                }
            });

            Planboard.initFormElements();

        },

        view: function()
        {

        },
        remove: function()
        {
            var d_id = Planboard.current_row;
            Planboard.renderConfirm('Afspraak verwijderen', 'Weet je zeker dat je deze afspraak wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/frontend_dev.php/admin/planboardData', {
                        parameters: {
                            method: 'delete',
                            id: d_id
                        },
                        onSuccess: function(transport) {
                            switch(transport.responseJSON.status) {
                                case 'failure':
                                    Planboard.renderAlert('Verwijderen van afspraak is niet gelukt. Probeer het later nog eens.');
                                    break;
                                case 'success':
                                    Planboard.update(true);
                                    break;
                            }
                            $('modal').removeClassName('active');
                        }

                    })
                },
                onCancel: function() {
                }
            });
        },

        listView: function()
        {
            $('marker-list').addClassName('active-marker');
            $('marker-map').removeClassName('active-marker');
            $('marker-grid').removeClassName('active-marker');
            $('view-map').hide();
            $('view-list').show();
            appointment_list.filterDate(Planboard.date, 3);
            //$('dropzone').hide();
        },
        mapView: function()
        {
            $('marker-list').removeClassName('active-marker');
            $('marker-map').addClassName('active-marker');
            $('marker-grid').removeClassName('active-marker');
            $('view-map').show();
            $('view-list').hide();
            $('map').show();
            //$('dropzone').show();
        },
        gridView: function()
        {
            $('marker-list').removeClassName('active-marker');
            $('marker-map').removeClassName('active-marker');
            $('marker-grid').addClassName('active-marker');
            $('view-map').show();
            $('view-list').hide();
            $('map').hide();
            //$('dropzone').show();
        },
        setAppointments: function(appointments) {
            this.apps = appointments;
        },

        setResourceMap: function(resource_map) {
            this.resource_map = resource_map;
        },




        addRow: function()
        {
            Planboard.renderMicroedit('Orderregel toevoegen', 'microedit-orderrow', {
                onSave: function()
                {
                    Planboard.saveRow(false);
                }
            });

            Planboard.initRowFormElements();
        },

        initRowFormElements: function()
        {
            Event.observe($('orderrow-type'), 'change', function() {
                Planboard.setOrderrowType($('orderrow-type').value);
            });

            Planboard.setOrderrowType($('orderrow-type').value);

            currency.initField($('orderrow-price'));

            var ac1 = new AutoComplete($('orderrow-description'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=products',
                onComplete: function() {
                    Planboard.setOrderrowType($('orderrow-type').value);
                    $('orderrow-price').value = accounting.formatMoney($('orderrow-price').value, "", 2, ".", ",");
                }
            });
        },

        setOrderrowType: function(type)
        {
            switch(type) {
                case 'product':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').hide();
                    $('orderrow-amount-container').show();
                    break;
                case 'service':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').hide();
                    $('orderrow-amount-container').show();
                    break;
                case 'hours':
                    $('orderrow-price-container').show();
                    $('orderrow-duration-container').show();
                    $('orderrow-amount-container').hide();
                    break;
            }
        },

        removeRow: function(which) {
            var current_row = which;
            Planboard.renderConfirm('Orderregel verwijderen', 'Weet je zeker dat je deze orderregel wilt verwijderen?', {
                onConfirm: function() {
                    new Ajax.Request('/admin/planboardData', {
                        parameters: {
                            form: 'orderrow',
                            method: 'delete',
                            id: current_row,
                            appointment_id: Planboard.current_row

                        },
                        onSuccess: function (transport) {

                            switch (transport.responseJSON.status) {
                                case 'success':
                                    Planboard.renderAlert('De orderregel is verwijderd.');
                                    $('modal-micro').removeClassName('active');
                                    Planboard.edit(Planboard.current_row);
                                    break;
                            }
                        }
                    });
                },
                onCancel: function() {

                }
            });
        },

        editRow: function(which) {
            var current_row = which;
            Planboard.renderMicroedit('Orderregel bewerken', 'microedit-orderrow', {
                onSave: function()
                {
                    Planboard.saveRow(current_row);
                }
            });

            d = Planboard.current_data.orderrows[which];
            $('orderrow-description').value = d.d;
            $('orderrow-price').value = d.p;
            $('orderrow-duration').value = d.c;
            $('orderrow-amount').value = d.c;
            $('orderrow-type').value = d.t;

            Planboard.initRowFormElements();


        },

        saveRow: function(row_id)
        {
            new Ajax.Request('/admin/planboardData', {
                parameters: {
                    form: 'orderrow',
                    method: 'save',
                    id: row_id,
                    appointment_id: Planboard.current_row,
                    description: $('orderrow-description').value,
                    price: $('orderrow-price').value,
                    duration: $('orderrow-duration').value,
                    amount: $('orderrow-amount').value,
                    type: $('orderrow-type').value
                },
                onSuccess: function(transport) {

                    switch(transport.responseJSON.status) {
                        case 'success':
                            Planboard.renderAlert(row_id?'De orderregel is gewijzigd.':'De orderregel is toegevoegd.');
                            $('modal-micro').removeClassName('active');
                            Planboard.edit(Planboard.current_row);
                            break;

                        case 'failure':
                            if(transport.responseJSON.errors) {
                                for(i in transport.responseJSON.errors) {
                                    $(i).addClassName('error');
                                };
                            }
                            else {
                                Planboard.renderAlert('Er is iets niet goed gegaan tijdens het opslaan. Probeer het later opnieuw.');
                                $('modal-micro').removeClassName('active');
                            }
                            break;
                    }


                },
                onFailure: function() {
                    $('modal-micro').removeClassName('active');
                }
            });
        }

    });

    Planboard = new PlanboardObject({});
    Planboard.setAppointments(appointments);
    Planboard.setResourceMap(resource_map);
    Planboard.render();

    var AppointmentList = Class.create(GenericList, {
        filterDate: function(date, dataCol) {

            if (!this.all_data) {
                this.all_data = this.data; // make backup of full list
            }

            tmp_data = [];
            var current_dataCol = 1;
            var current_date = date;

            for (i in this.all_data) {
                var tthis = this;
                if (this.all_data.hasOwnProperty(i)) {
                    row = this.all_data[i];
                    date = row[current_dataCol];
                    if(date==current_date) {
                        tmp_data.push(row);
                    }
                }
            }
            this.data = tmp_data;
            this.renderList();
        }
    });

    appointment_list = new AppointmentList({
        container: 'appointment-list',
        data_url: planboard_ajax_url,
        date_filter: true,
        headers: [
            'Datum',
            'Starttijd',
            'Eindtijd',
            'Medewerker',
            'Klant',
            'Adres'
        ],
        actions: {
            'edit': 'Planboard.edit',
            'remove': 'Planboard.remove'
        }
    });

    Event.observe($('list-date-filter'), 'change', function() {
        appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'keyup', function() {
        appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'blur', function() {
        appointment_list.filterDate(this.value, 3);
    });
    Event.observe($('list-date-filter'), 'focus', function() {
        appointment_list.filterDate(this.value, 3);
    });

});

Event.observe(window, 'resize', function() {
    of1 = $('content').cumulativeOffset();
    of2 = $('buttons').cumulativeOffset();
    diff = of2[1]-of1[1];

    $('content-inner').setStyle({height: diff + 'px'});

    Planboard.render();
});