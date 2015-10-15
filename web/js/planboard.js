var list_data = null;
var appointment_list = null;
var Planboard = null;

Event.observe(window, 'load', function() {
    var PlanboardObject = Class.create(AdminBase, {

        col_width: 0,
        col_height: 0,
        map: null,
        apps: [],
        date: null,
        hasDatePicker: false,
        resource_map: [],
        scheduledApps: [],
        unscheduledApps: [],
        settings: {
            dataURL: planboard_data_url
        },

        initialize: function (config) {
        },

        render: function()
        {
            this.calculateDimensions();
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

            //Event.observe($('appointment-add-link'), 'click', Planboard.new);
        },

        plotAppointments: function(date) {
            // date is in nl notation
            // first, clear all apps on the grid

            this.date = date;

            $$('.workorder').each(function(s) {
                $(s).remove();
            });

            if (this.apps[date]) {
                $(this.apps[date]).each(function(s, i) {
                    Planboard.plotAppointment(s, i);
                });
            }
        },

        calculateDimensions: function() {
            this.col_height = $('resources').getHeight();
            var s = $('planboard').getWidth() - $('resources').getWidth();
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
            return { lat: 52.1666348, lng: 4.5051274 };
        },

        getCompanyInfo: function() {
            return {
                name: 'Rijnstreek Verwarming B.V.'
            }
        },

        plotCompany: function() {
            var myLatlng = new google.maps.LatLng(Planboard.getCompanyLocation());

            var marker = new google.maps.Marker({
                position: myLatlng,
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

            return colors[color];
        },

        plotAppointment: function (s, i){
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
            var was_dragged = false;

            Planboard.scheduledApps[Planboard.scheduledApps.length] = appointment;
            Event.observe(div, 'click', function(event) {
                if (was_dragged) {
                    was_dragged = false;
                    return;
                }

                Planboard.edit(this.id.substr(10));
            });

            new Draggable(div.id, { revert: true,
                onStart: function(elem) {
                    app = Planboard.scheduledApps[elem.element.id.substr(10)];
                    $(elem.element).addClassName('dragging');
                },
                onEnd: function(elem)
                {
                    // todo: update position of app
                    $(elem.element).removeClassName('dragging');
                    was_dragged = true;
                }
            });



        },
        initUnscheduled:  function ()
        {
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
        },

        addUnscheduled: function(data)
        {
            this.unscheduledApps[this.unscheduledApps.length] = data;
        },

        initDirections: function()
        {
            var directionsDisplay;
            var directionsService = new google.maps.DirectionsService();

            directionsDisplay = new google.maps.DirectionsRenderer();

            directionsDisplay.setMap(Planboard.map);

            var waypts = [];
            waypts.push({
                location:new google.maps.LatLng('52.1480517','4.4532838'),
                stopover:true});
            waypts.push({
                location:new google.maps.LatLng('52.1463407','4.5373579'),
                stopover:true});


            var request = {
                origin: new google.maps.LatLng('52.14739','4.47702'),
                destination:new google.maps.LatLng('52.14739','4.47702'),
                travelMode: google.maps.TravelMode.DRIVING,
                waypoints: waypts,
                optimizeWaypoints: true
            };
            directionsService.route(request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    directionsDisplay.setDirections(response);
                }
            });
        },

        initUnschedule: function()
        {
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

            $$('#planning ul li ul li').each(function(s,i) {
                Droppables.add(s, {
                    accept: ['appointment','workorder'],
                    onDrop: function(element) {

                        o =  $(element).hasClassName('appointment') ? 4 : 10;

                        parts = ($(s).id.split('_'));
                        team_id = parts[1];
                        resource_id = parts[2];
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
                        offset = x - grid_start;
                        if (offset < 0) offset = 0;
                        // now, offset is relative to 8:00
                        hours_after_start = (offset / (Planboard.col_width + 1));
                        hours_after_start += 8;

                        hours = Math.floor(hours_after_start);
                        minutes = Math.round(60 * ( hours_after_start - hours), 2);
                        rem = minutes % 5;
                        minutes = minutes - rem;
                        var startTime = (hours < 10 ? '0' + hours : hours) + ':' + (minutes < 10 ? '0' + minutes : minutes);
                        sm = (hours * 60) + minutes;
                        sm += app_data.duration;
                        minutes_end = sm % 60;
                        hours_end = (sm - minutes_end) / 60;
                        var endTime = (hours_end < 10 ? '0' + hours_end : hours_end) + ':' + (minutes_end < 10 ? '0' + minutes_end : minutes_end);

                        if (Planboard.apps[Planboard.date]) {
                            $(Planboard.apps[Planboard.date]).each(function(s, i) {
                                if (s.id == app_data.id) {
                                    console.log(s);
                                    Planboard.apps[Planboard.date][i].start = startTime;
                                    Planboard.apps[Planboard.date][i].finish = endTime;
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
                    }
                });
            });
        },

        initFormElements: function()
        {
            if (!Planboard.hasDatePicker) {
                new MY.DatePicker({
                    input: 'appointment-date',
                    format: 'dd-MM-yyyy',
                    showWeek: true
                });

                var t1 = new Proto.TimePicker("appointment-time-from", { step: 5 });
                var t2 = new Proto.TimePicker("appointment-time-till", { step: 5 });

                var oldTime = t1.getTime();

                // Keep the duration between the two inputs.
                $("appointment-time-from").on('time:change', function() {
                    if ($("appointment-time-till").value) { // Only update when second input has a value.
                        // Calculate duration.
                        var duration = (t2.getTime() - oldTime);
                        var time = t1.getTime();
                        // Calculate and update the time in the second input.
                        t2.setTime(new Date(new Date(time.getTime() + duration)));
                        oldTime = time;
                    }
                });
                // Validate.
                $("appointment-time-till").on('time:change', function() {
                    if(t1.getTime() > t2.getTime()) {
                        this.addClassName("error");
                    }
                    else {
                        this.removeClassName("error");
                    }
                });

                Planboard.hasDatePicker = true;
            }

            var ac1 = new AutoComplete($('appointment-zipcode'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=zipcode'});
            var ac2 = new AutoComplete($('appointment-ctitle'), { data_url: '/frontend_dev.php/admin/customersData?form=search&method=customer'});
        },
        new: function()
        {
            Planboard.renderForm('Afspraak toevoegen', $('appointment-form'), {

            });

            Planboard.initFormElements();
        },

        edit: function(appointment)
        {
            //console.log(Planboard.)
            Planboard.renderForm('Afspraak bewerken', $('appointment-form'), {

            });

            Planboard.initFormElements();

            var app_id = appointment;
            if (this.apps[this.date]) {
                $(this.apps[this.date]).each(function(s,i) {
                    if(s.id == app_id) {
                        $('appointment-ctitle').value = s.customer;
                        $('appointment-title').value = s.title;
                        $('appointment-resource').value = s.resource;
                        $('appointment-time-from').value = s.start;
                        $('appointment-time-till').value = s.finish;
                        $('appointment-date').value = Planboard.date;
                        console.log(s);
                    }

                });
            }


        },

        view: function()
        {

        },
        remove: function()
        {

        },

        listView: function()
        {
            $('view-map').hide();
            $('view-list').show();
            $('dropzone').hide();
        },
        mapView: function()
        {
            $('view-map').show();
            $('view-list').hide();
            $('map').show();
            $('dropzone').show();
        },
        gridView: function()
        {
            $('view-map').show();
            $('view-list').hide();
            $('map').hide();
            $('dropzone').show();
        },
        setAppointments: function(appointments) {
            this.apps = appointments;
        },

        setResourceMap: function(resource_map) {
            this.resource_map = resource_map;
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
            'remove': 'Planboard.remove',
            'view': 'Planboard.view'
            //'documents': 'invoice.downloadInvoice'
        }
    });
});




