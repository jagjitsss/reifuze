<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Book Appointment</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="{{ asset('back/assets/css/mark-your-calendar.css') }}">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <style>
        .center {
            border-radius: 25px;
            margin: 10px;
            width: 60%;
            height: 650px;
            border: 2px solid #04AA6D;
            padding: 20px;
            background:#fcfcfc;
            float:right;
        }
        .input {
            border-radius: 25px;
            border: 1px solid #495057;
            padding: 20px; 
            width: 300px;
            height: 10px;
            margin:20px;    
        }

        .button {
          background-color: #04AA6D;
          border: none;
          color: white;
          padding: 20px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 16px;
          margin: 4px 2px;
        }
        .alert {
          padding: 20px;
          background-color: #04AA6D; /* Red */
          color: white;
          margin-bottom: 15px;
          width:500px;
          margin-left:35%;
          border-radius: 12px;
        }

        /* The close button */
        .closebtn {
          margin-left: 15px;
          color: white;
          font-weight: bold;
          float: right;
          font-size: 22px;
          line-height: 20px;
          cursor: pointer;
          transition: 0.3s;
        }

        /* When moving the mouse over the close button */
        .closebtn:hover {
          color: black;
        }


        .button4 {border-radius: 12px;}

        .timezones {
          height:100%;
          padding:15px;
        }
        .picker {
          float:left;
        }
        .dt {
          padding:10px;
          font-weight:bold;
          display:block;
          margin-bottom:5px;
        }
        .error {
          color: red;
          margin-bottom: 10px;
          padding-left: 10px;
        }

        .alert-error {
          padding: 20px;
          background-color: #ff0000;
          color: white;
          margin-bottom: 15px;
          width: 500px;
          margin-left: 35%;
          border-radius: 12px;
        }
        .book_appointment {
          height:650px;
        }
        /* .slot-disabled {
          opacity:0.5;
          pointer-events:none;
          background-color: #d3d3d3;
          border: #d3d3d3;
        } */

        .heading-css {
          text-align:center;
          margin-bottom:20px;
          width: 60%;
          float:right;
        }
        .title-ea {
          width: 37%;
          text-align: center;
          position: absolute;
          margin-bottom: 0;
          margin-top: 45px;
        }
        .left{
          width:30%;
          border-radius: 25px 10px 10px 25px;
          margin: 10px;
          height: 650px;
          max-height: 650px;
          border: 2px solid #04AA6D;
          padding: 20px;
          background: #fff;
          float: left;
          position: relative;
          margin-top: 5.9%;
          overflow-y:scroll;
        }

        .appt-card {
          border: 2px solid #04AA6D;
          border-radius:10px;
          margin-bottom: 10px;
          background-color:#f7f7f7;
        }
        .appt-card-body {
          padding-left: 10px;
        }
        .appt-u-title {
          margin-bottom: 0px;
        }

        .left::-webkit-scrollbar {
          background: #eee;
          width: 10px;
          border-radius:10px;
        }
        .left::-webkit-scrollbar-track {
          margin-right:15px;
        }
        .left::-webkit-scrollbar-thumb {
          padding: 0 4px;
          border-right: 0px solid transparent;
          border-left: 0px solid transparent;
          background: #909090;
          background-clip: padding-box;
          border-radius: 100px;
          /* &:hover {
            background: #606060;
          } */
        }
        .c-inner-text {
          color: #04AA6D;
        }

        .appt-buttons {
          margin-bottom:10px;
          display: flex;
          justify-content: center;
        }

        .cancel-btn {
          border : 2px solid #ff0000;
          border-radius:25px;
          padding : 10px 20px 10px 20px;
          color: #ff0000;
          cursor: pointer;
          margin-right: 5px;
        }

        .reschedule-btn {
          border : 2px solid #04AA6D;
          border-radius:25px;
          padding : 10px 20px 10px 20px;
          color: #04AA6D;
          cursor: pointer;
          margin-left: 5px;
        }

        .hide {
          display:none;
        }
        .s-msg {
          background: #04AA6D;
          color: #fff;
          padding: 12px;
          border-radius: 10px;
          display:none;
          text-align: center;
          margin: 0 auto;
          width:40%;
        }
        .e-msg {
          background: #ff0000;
          color: #fff;
          padding: 12px;
          border-radius: 10px;
          display:none;
          text-align: center;
          margin: 0 auto;
          width:40%;
        }
        .cal-div {
          display:flex;
          justify-content:center;
        }
        
    </style>
</head>
<body style="font-family: sans-serif;">
  <!-- <div class="main"> -->
    <div class="s-msg"></div>   
    <div class="e-msg"></div>
    @if(session('success'))
      <div class="alert">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        {{ session('success') }}
      </div>
    @elseif(session('error'))
      <div class="alert-error">
        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
        {{ session('error') }}
      </div>
    @endif

    <h3 class="title-ea">Existing Appointments</h3>
    <div class="left appointments-sec">
        @if($getUserAppointments)
          @foreach($getUserAppointments as $userAppointments)
            <div class="appt-card">
                <div class="appt-card-body">
                  
                  <!-- setting id of appointment -->
                  <input type="hidden" class="appt_id" name="appt_id" value="{{$userAppointments->id}}">
                  <input type="hidden" class="previous_date" name="previous_date" value="{{ date('Y-m-d', strtotime($userAppointments->appt_date)) }}">
                  <input type="hidden" class="previous_time" name="previous_time" value="{{ date('H:i', strtotime($userAppointments->appt_time)) }}">


                  <!-- <h4 class="appt-u-title"><b>Name: </b> {{ $userAppointments->name }}</h4>
                  <p class="appt-u-email"><b>Email: </b>{{ $userAppointments->email }}</p>
                  <p class="appt-u-mobile"><b>Phone: </b>{{ $userAppointments->mobile }}</p> -->
                  <p class="appt-u-datetime"><b class="c-inner-text">When: </b>{{ date('M j Y', strtotime($userAppointments->appt_date)) }}, {{ date('H:i', strtotime($userAppointments->appt_time)) }} ({{ $userAppointments->timezone }})</p>
                  <p class="appt-u-description"><b class="c-inner-text">Purpose: </b>{{ $userAppointments->description }}</p>
                  <div class="appt-buttons">
                    <button type="button" class="cancel-btn">Cancel</button>
                    <button type="button" class="reschedule-btn">Reschedule</button>
                  </div>
                </div>
            </div>
          @endforeach
        @endif
        
    </div>

    <h1 class="heading-css">Book Appointment</h1>
    <div class="center">
      
      <form class="book_appointment" method="POST" action="{{ route('appointments.store') }}">
        @csrf
     
        <div>
          <div class="form-group">
            <label style="padding:10px;font-weight:bold">Time Zone</label>
            <div class="input-group">
              <select class="input form-control timezones" name="timezone" required>
              </select>
              @error('timezone')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div  class="form-group">
            <label class="dt">Select Date Time</label>
            <input type="hidden" class="appt_date" name="appt_date" value="{{old('appt_date')}}" required>
            <input type="hidden" class="appt_time" name="appt_time" value="{{old('appt_time')}}" required>
            <div class="calendar-div">
            
              @error('appt_date')
                        <div class="error">{{ $message }}</div>
              @enderror
              @error('appt_time')
                  <div class="error">{{ $message }}</div>
              @enderror
              <div class="picker"></div>

            </div>
          </div>
        </div>
        <div style="float:right;position: relative; margin-top: -145px;right:60px">
          <div  class="form-group">
            <label style="padding:10px;font-weight:bold">Full Name</label>
            <div class="input-group">
              <input type="text"  class="input name" placeholder="Full Name"
                name="name" id="name" value="{{old('name')}}"  required>
              @error('name')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-group">
            <label style="padding:10px;font-weight:bold">Mobile</label>
            <div class="input-group">
              <input type="tel"  class="input mobile" placeholder="Mobile"
                name="mobile" id="mobile" value="{{old('mobile')}}" minlength="10" maxlength="10" required>
              @error('mobile')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-group">
            <label style="padding:10px;font-weight:bold">Email</label>
            <div class="input-group">
              <input type="email" class="input email" placeholder="Email"
                name="email" id="email" value="{{old('email')}}" required>

              @error('email')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-group">
            <label style="padding:10px;font-weight:bold">What do you hope to get out of this call?</label>
            <div class="input-group">
              <textarea type="text"  class="input description" style="height:100px" placeholder="What do you hope to get out of this call?"
                name="description" id="description" required>{{old('description')}}</textarea>
              
              @error('description')
                <div class="error">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="form-group">
            <button type="submit" class="button button4">Confirm Booking</button>
          </div>
        </div>

        
      </form>
    </div>

    <!-- Cancel appointment modal -->
    <div class="cancel_appt_modal hide" title="Cancel Appointment">
        <div class="modal-body">  
               
            <p>Are you sure you want to cancel this appointment?</p>
        </div>
    </div>
    <!-- End of Cancel appointment modal -->

    <!-- Reschedule Appointment modal -->
    <div class="reschedule_appt_modal hide" title="Reschedule Appointment">
        <div class="modal-body">  
          <p><strong>Please select date and time for reschedule appointment.</strong></p>
          <div class="cal-div">
            <!-- <input type="hidden" class="reschedule_time" name="reschedule_time" value="">
            <input type="hidden" class="reschedule_date" name="reschedule_date" value=""> -->
            <div class="picker"></div>
          </div>
        </div>
    </div>
    <!-- End of Reschedule Appointment modal -->
  <!-- </div> -->

  <script src="//code.jquery.com/jquery-3.7.1.min.js"></script> 
  <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.min.js"></script> -->
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <script src="//code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
  <script src="{{ asset('back/assets/js/pages/timezones.full.js') }}"></script>
  <script src="{{ asset('back/assets/js/pages/mark-your-calendar.js') }}"></script>

  <script>
    $(document).ready(function() {
      $('.timezones').timezones();

      // function of calendar
        (function($) {
          $('.picker').markyourcalendar({
            availability:[
              ['01:00', '02:00', '03:00', '04:00', '05:00'],
              ['02:00'],
              ['03:00'],
              ['04:00'],
              ['05:00'],
              ['06:00'],
              ['07:00']
            ],
            isMultiple: false,
            onClick: function(ev, data) {
              // data is a list of datetimes
              $('.myc-available-time').css({'pointer-events':'all', 'opacity':'1'})
              console.log(data);
              var html = ``;
              $.each(data, function() {
                var d = this.split(' ')[0];
                var t = this.split(' ')[1];
                // html += `<p>` + d + ` ` + t + `</p>`;
                 console.log(t); 
                // setting values of date & time in input fields
                $('.appt_date').val(d);
                $('.appt_time').val(t);

                
              });

              // $('#selected-dates').html(html);
            },
            onClickNavigator: function(ev, instance) {
              var arr = [

                [
                  ['01:00', '02:00', '03:00', '04:00', '05:00'],
                  ['02:00'],
                  ['03:00'],
                  ['04:00'],
                  ['05:00'],
                  ['06:00'],
                  ['07:00']
                ],
                [
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['01:00', '05:00'],
                  ['02:00', '05:00'],
                  ['03:30'],
                  ['02:00', '05:00'],
                  ['02:00', '05:00'],
                  ['02:00', '05:00']
                ],
                [
                  ['02:00', '05:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['04:00', '05:00'],
                  ['02:00', '05:00'],
                  ['02:00', '05:00'],
                  ['02:00', '05:00'],
                  ['02:00', '05:00']
                ],
                [
                  ['04:00', '05:00'],
                  ['04:00', '05:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00']
                ],
                [
                  ['04:00', '05:00'],
                  ['04:00', '05:00'],
                  ['04:00', '05:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['04:00', '05:00'],
                  ['04:00', '05:00'],
                  ['04:00', '05:00']
                ],
                [
                  ['04:00', '06:00'],
                  ['04:00', '06:00'],
                  ['04:00', '06:00'],
                  ['04:00', '06:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['04:00', '06:00'],
                  ['04:00', '06:00']
                ],
                [
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['03:00', '06:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00'],
                  ['03:00', '06:00']
                ],
                [
                  ['03:00', '04:00'],
                  ['03:00', '04:00'],
                  ['03:00', '04:00'],
                  ['03:00', '04:00'],
                  ['03:00', '04:00'],
                  ['03:00', '04:00'],
                  ['04:00', '05:00', '06:00', '07:00', '08:00']
                ]
              ]
              var rn = Math.floor(Math.random() * 10) % 7;
              instance.setAvailability(arr[rn]);
            }

            
          });

          // Function to get available slots based on booking data and current date
          // function getAvailableSlots() {
          //   const currentDate = new Date();
          //   // currentDate.toLocaleString({
          //   //   timeZone: 'Asia/Calcutta'
          //   // });
          //   // console.log(currentDate);

          //   // Get year, month, and day part from the date
          //   // var year = currentDate.toLocaleString("default", { year: "numeric" });
          //   // var month = currentDate.toLocaleString("default", { month: "2-digit" });
          //   // var day = currentDate.toLocaleString("default", { day: "2-digit" });

          //   // var todayDate = year + "-" + month + "-" + '07';
          //   // var todayDate = currentDate.getTime();
          //   // console.log(todayDate);


          //   const bookedAppointments = 
          //     // booked appointment data
          //     {!! $bookedSlots !!} ;
          //     console.log(bookedAppointments);

          //   // Create a matrix to represent availability slots
          //   const availableSlots = [
          //     ['01:00', '02:00', '03:00', '04:00', '05:00'],
          //     ['02:00'],
          //     ['03:00'],
          //     ['04:00'],
          //     ['05:00'],
          //     ['06:00'],
          //     ['07:00']
          //   ];

            
          //   // console.log(currentDate.getHours());
          //   // console.log(currentDate.getDate());
          //   // Iterate through booked appointments and remove slots that are booked or in the past
          //   for (const appointment of bookedAppointments) {
          //     const apptDate = new Date(appointment.appt_date);
          //     // const apptTime = parseInt(appointment.appt_time.split(':')[0]);
          //     console.log(appointment.appt_date);
          //     // console.log("today : "+ todayDate);
          //     // console.log(appointment.b_date);
          //     // console.log("date : "+ currentDate.getDate());
          //     // console.log(appointment.b_time);
          //     console.log("time : "+ currentDate.getHours());
          //     console.log(appointment.appt_time);
          //     // console.log("time : "+ currentDate.getHours());
          //     if (
          //       // apptDate < currentDate 
          //       // ||
          //       // (
          //         // apptDate.getTime() == currentDate.getTime()
          //         //  &&
          //         appointment.b_time <= currentDate.getHours()
          //         // )
          //     ) 
              
          //     // if (
          //     //   apptDate < currentDate ||
          //     //   (apptDate.getTime() === currentDate.getTime() && apptTime <= currentDate.getHours())
          //     // ) 
          //     {
                

          //       console.log("ok");
          //       // Mark the slot as booked or in the past
          //       const rowIndex = availableSlots.findIndex(
          //         slots => slots.includes(appointment.appt_time)
                  
          //       );
          //       console.log(rowIndex);
          //       if (rowIndex !== -1) {
          //         const columnIndex = availableSlots[rowIndex].indexOf(
          //           appointment.appt_time
          //         );
          //         if (columnIndex !== -1) {
          //           availableSlots[rowIndex].splice(columnIndex, 1);
                    
          //         }
          //       }
          //     }
          //   }
          //   console.log(availableSlots);
          //   return availableSlots;
          // }

        })(jQuery);


        // hiding the past slots
        // var dateTime = new Date();
        // var currentTime = dateTime.getHours() + ":" + dateTime.getMinutes();
        // var calTimeSlot = $('.myc-day-time-container a').attr('data-time');
        // console.log(currentTime);
        // console.log(calTimeSlot);

        // if(calTimeSlot < currentTime) {
        //   $('.myc-day-time-container a[data-time="'+calTimeSlot+'"]').hide();
        // }
        
        
    });
  </script>

  <script>
    $(document).ready(function() {

      // already booked slots today
    
      var Slots = {!! $bookedSlots !!}; //directly output the value of $bookedSlots

      /**
        * loop through all the slots
        * passing json object in each loop
      */
      $.each(Slots, function(index,slot) {
        
        // console.log(slot);
        // disbale slots based on attribute value of anchor tag
        $('.myc-day-time-container a[data-time="'+slot.appt_time+'"][data-date="'+slot.appt_date+'"]').hide();
        
      });

    });
    
    
   


  </script>

  <script>

    $(document).ready(function() {

      // setup ajax token
      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });
    })
    

    // open modal when click on cancel button
    $('.cancel-btn').on('click', function(){

      $( ".cancel_appt_modal" ).dialog({
        width: 500,
        draggable: false,
        modal:true,
        create: function( event, ui ) {
          $('body').css({overflow: 'hidden'})
        },
        beforeClose: function(event, ui) {
          $('body').css({ overflow: 'inherit' })
        },
        buttons: {
          "Yes": function() {
            
            var appt_id = $('.appt_id').val(); // appointment id
            // alert(appt_id);
            // Ajax for cancelling appointment
            $.ajax({
                type: "POST",
                url: "{{ route('appointments.cancelAppointment') }}",
                data: {id: appt_id},
                dataType: "json",
                success: function(data) {
                      console.log(data);
                    // Ajax call completed successfully
                    if(data.success == 1) {
                        $('.cancel_appt_modal').dialog( "close" );
                        $('.s-msg').html(data.message);
                        $('.s-msg').show();
                        $('.e-msg').hide();

                        setTimeout(() => {
                          window.location.reload();
                          
                        }, 2500);

                        
                    } else {
                        $('.cancel_appt_modal').dialog( "close" );
                        $('.s-msg').hide();
                        $('.e-msg').html(data.message);
                        $('.e-msg').show();

                    }

                },
                error: function(data) {
                      
                    // Some error in ajax call
                    $('.cancel_appt_modal').dialog( "close" );
                    $('.s-msg').hide();
                    $('.e-msg').html(data.message);
                    $('.e-msg').show();
                }
            });


          },
          "No": function() {
            $( this ).dialog( "close" );
          }
        }
      });
      
    });


    // open modal when click on cancel button
    $('.reschedule-btn').on('click', function(){

        var previousDate = $('.previous_date').val();
        // alert(previousDate);
        var previousTime = $('.previous_time').val();
        // alert(previousTime);

      
        // $('.reschedule_appt_modal modal-body').find('.myc-available-time').addClass('selected');
        $( ".reschedule_appt_modal" ).dialog({
          width: 500,
          draggable: false,
          modal:true,
          create: function( event, ui ) {
            $('body').css({overflow: 'hidden'});
            $(this).find('a[data-time="'+previousTime+'"][data-date="'+previousDate+'"]').removeAttr('style');
            $(this).find('a[data-time="'+previousTime+'"][data-date="'+previousDate+'"]').css({'pointer-events':'none','opacity':'0.5'});
            $(this).find('a[data-time="'+previousTime+'"][data-date="'+previousDate+'"]').addClass('selected');
          },
          beforeClose: function(event, ui) {
            $('body').css({ overflow: 'inherit' });
            $(this).find('a').removeClass('selected');
            $(this).find('a[data-time="'+previousTime+'"][data-date="'+previousDate+'"]').css({'pointer-events':'none','opacity':'0.5'});
            $(this).find('a[data-time="'+previousTime+'"][data-date="'+previousDate+'"]').addClass('selected');

          },
          buttons: {
            "Update": function() {
              
              var appt_id = $('.appt_id').val(); // appointment id
              var rescheduleDate = $('.appt_date').val(); // date
              var rescheduleTime = $('.appt_time').val(); // time
              // alert(appt_id);
              // Ajax for cancelling appointment
              $.ajax({
                  type: "POST",
                  url: "{{ route('appointments.reschduleAppointment') }}",
                  data: {
                    id: appt_id,
                    appt_date: rescheduleDate,
                    appt_time: rescheduleTime,
                  },
                  dataType: "json",
                  success: function(data) {
                        console.log(data);
                      // Ajax call completed successfully
                      if(data.success == 1) {
                          $('.reschedule_appt_modal').dialog( "close" );
                          $('.s-msg').html(data.message);
                          $('.s-msg').show();
                          $('.e-msg').hide();

                          setTimeout(() => {
                            window.location.reload();
                            
                          }, 2500);
   
                      } else {
                          $('.reschedule_appt_modal').dialog( "close" );
                          $('.s-msg').hide();
                          $('.e-msg').html(data.message);
                          $('.e-msg').show();

                      }

                  },
                  error: function(data) {
                        
                      // Some error in ajax call
                      $('.reschedule_appt_modal').dialog( "close" );
                      $('.s-msg').hide();
                      $('.e-msg').html(data.message);
                      $('.e-msg').show();
                  }
              });


            },
            "Close": function() {
              $( this ).dialog( "close" );
            }
          }
        });

        });


  

  </script>
</body>
</html>