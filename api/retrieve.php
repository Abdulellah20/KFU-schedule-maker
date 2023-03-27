
<?php
include 'schedule.html';
// Declare variables outside the function
$url = 'https://banner.kfu.edu.sa:7710/KFU/ws?p_trm_code=144325&p_col_code=09&p_sex_code=12';

$start_table = '';
$end_table = '';

function updateTable() {
  // Get the selected values of gender and major dropdowns
  $selectedGender = isset($_POST['gender']) ? $_POST['gender'] : null;
  $selectedMajor = isset($_POST['major']) ? $_POST['major'] : null;


        
  // Perform the necessary update to the table based on the selected values
  global $url, $start_table, $end_table;
  
  if ($selectedGender == 'Female') {
    $url = 'https://banner.kfu.edu.sa:7710/KFU/ws?p_trm_code=144325&p_col_code=09&p_sex_code=12';
    switch ($selectedMajor) {
      case 'CS':
        $start_table = 149;
        $end_table = 250;
        break;
      case 'IS':
        $start_table = 252;
        $end_table = 306;
        break;
      case 'CN':
        $start_table = 319;
        $end_table = 369;
        break;
      case 'CE':
        $start_table = 308;
        $end_table = 317;
        break;
      case 'CS-old':
        $start_table = 3;
        $end_table = 56;
        break;
      case 'IS-old':
        $start_table = 64;
        $end_table = 120;
        break;
      case 'CE-old':
        $start_table = 122;
        $end_table = 141;
        break;
      case 'CN-old':
        $start_table = 143;
        $end_table = 147;
        break;
    }
  } else if ($selectedGender == 'Male') {
    $url = 'https://banner.kfu.edu.sa:7710/KFU/ws?p_trm_code=144325&p_col_code=09&p_sex_code=11';
    switch ($selectedMajor) {
      case 'CS':
        $start_table = 180;
        $end_table = 317;
        break;
      case 'IS':
        $start_table = 319;
        $end_table = 407;
        break;
      case 'CN':
        $start_table = 450;
        $end_table = 516;
        break;
      case 'CE':
        $start_table = 409;
        $end_table = 448;
        break;
      case 'CS-old':
        $start_table = 3;
        $end_table = 79;
        break;
      case 'IS-old':
        $start_table = 81;
        $end_table = 139;
        break;
      case 'CE-old':
        $start_table = 141;
        $end_table = 146;
        break;
      case 'CN-old':
        $start_table = 148;
        $end_table = 178;
        break;
    }
  }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  updateTable();
}








$html = file_get_contents($url);
$dom = new DOMDocument();
@$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
$tables = $dom->getElementsByTagName('table');
$table_counter = 1;
$original_data = array();
$converted_days = array();
$converted_times = array();
$course_numbers = array();
$crns = array();
$sections = array();
$types = array();
$instructors = array();
$state = array();



foreach ($tables as $table) {
  if ($table_counter >= $start_table && $table_counter <= $end_table) {
    $rows = $table->getElementsByTagName('tr');
    foreach ($rows as $row) {
      $cells = $row->getElementsByTagName('td');
      $cell_counter = 1;
      $original_row = array();
      $day = "";
      $time = "";
      foreach ($cells as $cell) {
        if ($cell_counter == 1) {
          $course_numbers[] = $cell->nodeValue;
        }
        if ($cell_counter == 4) {
          $state[] = $cell->nodeValue;
        }
        if ($cell_counter == 10) {
          $instructors[] = $cell->nodeValue;
        }
        if ($cell_counter == 2) {
          $crns[] = $cell->nodeValue;
        }
        if ($cell_counter == 3) {
          $sections[] = $cell->nodeValue;
        }
        if ($cell_counter == 8) {
          $types[] = $cell->nodeValue;
        }
        if ($cell_counter == 5) {
          $cell5 = $cell->nodeValue;
          $converted_cell5[] = $cell5;
        }
        if ($cell_counter == 7) {
          $day = trim($cell->nodeValue);

          if (strpos($day, "ح") !== false && strpos($day, "ث") !== false && strpos($day, "خ") !== false) {
              $day = "SUN TUE THU";
          } else if (strpos($day, "ن") !== false && strpos($day, "ر") !== false) {
              $day = "MON WED";
          } else if (strpos($day, "ر") !== false && strpos($day, "خ") !== false) {
              $day = "WED THU";
          } else if (strpos($day, "ح") !== false && strpos($day, "خ") !== false) {
              $day = "SUN THU";
          } else {
              switch ($day) {
                  case "ح":
                      $day = "SUN";
                      break;
                  case "ن":
                      $day = "MON";
                      break;
                  case "ث":
                      $day = "TUE";
                      break;
                  case "ر":
                      $day = "WED";
                      break;
                  case "خ":
                      $day = "THU";
                      break;
                  default:
                      $day = "Day";
                      break;
              }
          }
          $converted_days[] = $day;
          
        }
        if ($cell_counter == 9) {
          $time = $cell->nodeValue;
          $converted_times[] = $time;
        }
        $original_row[] = $cell->nodeValue;
        $cell_counter++;
      }
      $original_data[] = $original_row;
    }
  }
  $table_counter++;
}
  echo '<table class="scraped-table">';
  for($i = 0; $i < count($original_data); $i++) {
    echo '<tr>';
    echo '<td class="crn">' . $crns[$i] . '</td>';
    echo '<td class="section">' . $sections[$i] . '</td>';
    echo '<td class="state">' . $state[$i] . '</td>';
    echo '<td class="name">' . $converted_cell5[$i] . '</td>';
    echo '<td class="time">' . $converted_times[$i] . '</td>';
    echo '<td class="day">' . $converted_days[$i] . '</td>';
    echo '<td class="type">' . $types[$i] . '</td>';
    echo '<td class="instructors">' . $instructors[$i] . '</td>';
    echo '<td class="course-number">' . $course_numbers[$i] . '</td>';
    echo '<td><div class="plus-button"><i class="fa-solid fa-plus"></i></div></td>';
    echo '</tr>';
}
echo '</table>';

?>


<script>
    const schedule = document.getElementById("schedule-table");
    const times = ["0730 - 0845", "0900 - 1015", "1030 - 1145", "1230 - 1345", "1400 - 1515", "1530 - 1645",  "1700 - 1815" ,"2300 - 2350"];

    for (let i = 0; i < times.length; i++) {
      const row = schedule.insertRow();
      const timeCell = row.insertCell();
      timeCell.innerHTML = times[i];
      for (let j = 0; j < 5; j++) {
        const dayCell = row.insertCell();
        dayCell.innerHTML = "";
      }
    }


    document.querySelector('#myForm input[type="submit"]').addEventListener('click', function(event) {
  event.preventDefault();
  document.querySelector('#myForm').submit();
});

// Get the select element and OK button
const select = document.getElementById('major');
const gender = document.getElementById('gender');
const okButton = document.getElementById('ok-button');

// Set the select's value to the saved value, if any
select.value = localStorage.getItem('selectedMajor') || 'CS';
gender.value = localStorage.getItem('selectedGender') || 'Female';

// Save the selected value to localStorage and submit the form when the OK button is clicked
okButton.addEventListener('click', () => {
  localStorage.setItem('selectedMajor', select.value);
  localStorage.setItem('selectedGender', gender.value);

  select.form.submit();
});

// Update the table whenever the select's value changes
select.addEventListener('change', () => {
  updateTable();
});

gender.addEventListener('change', () => {
  updateTable();
});

  </script>


<script>
const scheduleTable = document.getElementById("schedule-table");
const daySelector = ".day";
const timeSelector = ".time";
const nameSelector = ".name";
const crnSelector = ".crn";
const typeSelector = ".type";
const plusButtons = document.querySelectorAll(".plus-button");

// object to keep track of occupied time slots
const occupied = {};

plusButtons.forEach(function(plusButton) {
  plusButton.addEventListener("click", function() {
    const days = plusButton.parentNode.parentNode.querySelector(daySelector).textContent.trim().split(' ');
    const time = plusButton.parentNode.parentNode.querySelector(timeSelector).textContent.trim();
    const name = plusButton.parentNode.parentNode.querySelector(nameSelector).textContent.trim();
    const crn = plusButton.parentNode.parentNode.querySelector(crnSelector).textContent.trim();
    const type = plusButton.parentNode.parentNode.querySelector(typeSelector).textContent.trim();

    console.log(`Days: ${days}, Time: ${time}, Name: ${name}, Type: ${type}`);

    for (let i = 0; i < days.length; i++) {
      const day = days[i];
      if (occupied[day] && occupied[day][time]) {
        alert(`Conflict: ${name} cannot be added at ${day} ${time}`);
        return;
      }

      let row;
      for (let i = 1; i < scheduleTable.rows.length; i++) {
        if (scheduleTable.rows[i].cells[0].innerHTML === time) {
          row = scheduleTable.rows[i];
          break;
        }
      }

      if (!row) {
        console.error(`No matching time found for time ${time}`);
        return;
      }

      let col;
      for (let i = 1; i < scheduleTable.rows[0].cells.length; i++) {
        if (scheduleTable.rows[0].cells[i].innerHTML === day) {
          col = i;
          break;
        }
      }

      let box = document.createElement("div");
      let text = document.createElement("p");
      text.textContent = name;
      text.style.backgroundColor = "transparent";
      box.classList.add("selected-box");
      box.appendChild(text);

      let removeButton = document.createElement("button");
      removeButton.innerHTML = '<i class="fa-regular fa-trash-can"></i>';
      removeButton.classList.add("remove-button");

      // add CRN data attribute to box
      box.setAttribute("data-crn", crn);

      removeButton.addEventListener("click", function() {
        let crn = box.getAttribute("data-crn");
        box.remove();

        // remove corresponding paragraph element
        let selectedCourse = document.querySelector(`#selected-courses [data-crn="${crn}"]`);
        if (selectedCourse) {
          selectedCourse.remove();
        }

        // update occupied time slots object
        for (let i = 0; i < days.length; i++) {
          const day = days[i];
          if (occupied[day] && occupied[day][time]) {
            delete occupied[day][time];
          }
        }
      });

      box.appendChild(removeButton);
      row.cells[col].appendChild(box);

    // update occupied time slots object
    if (!occupied[day]) {
      occupied[day] = {};
    }
    occupied[day][time] = true;

    box.addEventListener("click", function() {
      removeButton.classList.toggle("visible");
    });

    // change the plus button to check icon
    plusButton.innerHTML = '<i class="fa-solid fa-check"></i>';

      // For selected courses
      let selectedcourses = document.getElementById("selected-courses");

      let selectedCourse = document.querySelector(`#selected-courses [data-crn="${crn}"]`);
      if (!selectedCourse) {
        selectedcourses.innerHTML = selectedcourses.innerHTML + `<div style="display:inline-block; margin: 8px;" data-crn="${crn}">
          <p>${name} <br> ${type} <br> CRN: ${crn} <br> <button id="copy-button-${crn}">Copy</button></p>
          <span id="copy-message-${crn}"></span>
        </div>`;
      }

      let copyButtons = document.querySelectorAll(`#selected-courses [id^="copy-button-"]`);
copyButtons.forEach(function(copyButton) {
  let crn = copyButton.id.replace("copy-button-", "");
  let crnInfo = `${crn}`;
  copyButton.addEventListener("click", function() {
    // Get the text to copy
    let courseInfo = `${crn}`;

    // Write the text to the clipboard
    navigator.clipboard.writeText(courseInfo).then(function() {
      // Set copy message and button icon and style
      let copyMessage = document.querySelector(`#copy-message-${crn}`);
      copyMessage.textContent = "Copied!";
      copyButton.innerHTML = '<i class="fa-solid fa-check"></i>';
      copyButton.style.backgroundColor = "transparent";
      copyButton.style.fontSize = "20px";

      // Revert copy message and button icon and style after 3 seconds
      setTimeout(function() {
        copyMessage.textContent = "";
        copyButton.innerHTML = 'Copy';
        copyButton.style.backgroundColor = "#777";
        copyButton.style.fontSize = "14px";
      }, 3000);
    }, function() {
      console.error("Failed to copy.");
    });
  });
});


}});




  });

</script>

<script>
  // functions for the Save as image button

  // Get the save button element
let saveButton = document.getElementById("save-button");

// Add a click event listener to the button
saveButton.addEventListener("click", function() {
  // Get the table element
  let table = document.getElementById("schedule-table");

  // Use the html2canvas library to create a canvas element from the table
  html2canvas(table).then(function(canvas) {
    // Convert the canvas to a PNG image
    let image = canvas.toDataURL("image/png");

    // Create a temporary link element to download the image
    let link = document.createElement("a");
    link.href = image;
    link.download = "Schedule.png";

    // Add the link to the document and click it to download the image
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });
});

</script>

<style>
  @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@300&display=swap');

*{
  background-color: rgb(41, 41, 41);
  font-family: 'Noto Sans Arabic', sans-serif;
  font-weight: bold;
  }


  
  #schedule-table {
    width: 50%;
    margin: auto;
    border-collapse: collapse;
    color: #fff;
    border-radius: 5px;
    box-shadow: 0px 0px 10px #000;
    table-layout: fixed;

  }
  
  #save-button {
    float: right;
    margin-right: 13%;
    padding: 10px;
    height: 20%;
    width: 10%;
    background-color: #777;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 15px;
  }
  #save-button:hover {
    background-color: rgb(88, 88, 88);
  }
  #save-button .fa-floppy-disk {
    color: black;
    margin: 5px;
    font-size: 25px;
    background-color: transparent;
  }

th, td {
border: 1px solid #777;
text-align: center;
padding: 8px;
align-items: center;
justify-content: center;
}
th {
background-color: grey;
}

.form-container {
  width: 65%;
  height: auto;  
  margin: auto;
  border: 1px solid #777;
  border-radius: 5px;
  box-shadow: 0px 0px 10px #000;
  justify-content: center;
  align-items: center;
  
}

label {

  font-size: 16px;
  color: #fff;
  padding: 15px;
  justify-content: center;
  align-items: center;
  margin: auto;
}

select {

  width: 20%;
  height: 25%;
  font-size: 13px;
  padding: 4px;
  background-color: #777;
  border: 1px solid #999;
  border-radius: 5px;
  color: #fff;
  margin: auto;
margin-top: 30px;
justify-content: center;
align-items: center;
margin-left: 35px;

}

select:hover{
  background-color: rgb(88, 88, 88);
}

#ok-button{
  font-size: 15px;
  margin-left: 25px;
  height: auto;
  width: 5%;
  padding: 4px;
  background-color: #777;
  border: 1px solid #999;
  border-radius: 5px;
  color: #fff;
  cursor: pointer;
  transition: all 0.1s ease-in-out;
  margin-bottom: 20px;
}


#ok-button:active {
  transform: scale(1.3);
}

input#ok-button:hover {
  background-color: rgb(88, 88, 88);

}

.scraped-table {
border-collapse: collapse;
color: white;
width: 65%;  
height: 5%;  
margin: auto;
margin-top: 30px;
direction: rtl;
border-radius: 5px;
box-shadow: 0px 0px 10px #000;
justify-content: center;
align-items: center;

}

.scraped-table td,
.scraped-table th{

  border: 1px solid #777;
padding: 10px;
width: 120px;
height: 40px;
text-align: center;
flex: 1;
font-size: 15px;
}
.scraped-table tr td:last-child {
height: 5%;
width: 5%;
}
.plus-button {
background-color: #777;
border-radius: 50%;
width: 30px;
height: 30px;
display: flex;
margin: auto;
justify-content: center;
align-items: center;
cursor: pointer;
font-size: 20px;
}
.plus-button .fa-solid{
background-color: transparent;
font-size: 20px;
}

.plus-button:hover {
background-color: rgb(88, 88, 88);
}

.plus-sign {
color: #fff;
text-align: center;

}
.selected-box {
width: 120px;
height: 30px;
background-color: #0099F8;
border-radius: 10px;
display: flex;
align-items: center;
justify-content: center;
margin: 10px;
padding: 5px;
color: white;
font-size: 15px;
display: table-cell;
}
.selected-box:hover {
cursor: pointer;
background-color: rgb(88, 88, 88);
}

#selected-courses {
text-align: center;
color: white;
}
button[id^="copy-button-"] {
border: none;
border-radius: 4px;
font-size: 14px;
background-color: #777;
color: #fff;
cursor: pointer;
margin-left: 8px;
}
button[id^="copy-button-"]:hover{
background-color: rgb(88, 88, 88);
}

span[id^="copy-message-"] {
display: inline-block;
font-size: 14px;
margin-left: 8px;
}

.remove-button {
color:  red;
font-size: 17px;
border: none;
position: relative;
padding: 5px;
height: auto;
width: auto;
background-color: transparent;
cursor: pointer;
float: right;
display: none;
}
.remove-button .fa-regular{
background-color: transparent;
color:  red;
}
.remove-button .fa-regular:hover {
color: white;
}
.visible {
display: block;
}
#copy-button .fa-solid{
background-color: transparent;
font-size: 20px;
}

</style>
