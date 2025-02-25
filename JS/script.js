const urlBase = 'http://group7.brycejensenucf.website/API';
const extension = 'php';
let userId = 0;
let firstName = "";
let lastName = "";

window.onload = function() {
    loadContacts();
};

function doLogin() {
    userId = 0;
    firstName = "";
    lastName = "";

    let login = document.getElementById("loginName").value;
    let password = document.getElementById("loginPassword").value;

    document.getElementById("loginResult").innerHTML = "";

    let tmp = { login: login, password: password };
    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + '/Login.' + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    try {
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                let jsonObject = JSON.parse(xhr.responseText);
                userId = jsonObject.id;

                if (userId < 1) {
                    document.getElementById("loginResult").innerHTML = "User/Password combination incorrect";
                    return;
                }

                firstName = jsonObject.firstName;
                lastName = jsonObject.lastName;

                saveCookie();

                // Redirect to the contacts dashboard after successful login
                window.location.href = "contactFunctions.html";
            }
        };
        xhr.send(jsonPayload);
    } catch (err) {
        document.getElementById("loginResult").innerHTML = err.message;
    }
}

function addUser() {
    let newUsername = document.getElementById("Createusername").value;
    let newPassword = document.getElementById("Createpassword").value;

    document.getElementById("registerResult").innerHTML = "";

    let tmp = { login: newUsername, password: newPassword };
    let jsonPayload = JSON.stringify(tmp);

    let url = urlBase + "/addUser." + extension;

    let xhr = new XMLHttpRequest();
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");

    xhr.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById("registerResult").innerHTML = "User registered!";
        }
    };

    try {
        xhr.send(jsonPayload);
    }
    catch (err) {
        document.getElementById("registerResult").innerHTML = err.message;
    }
}

function doLogout()
{
	userId = 0;
	firstName = "";
	lastName = "";
	document.cookie = "firstName= ; expires = Thu, 01 Jan 1970 00:00:00 GMT";
	window.location.href = "index.html";
}

function addContact()
{
	let name = document.getElementById("name").value;
	let number = document.getElementById("phonenumber").value;
	let email = document.getElementById("email").value;
	document.getElementById("contactAddResult").innerHTML = "";

	let tmp = {name: name, number: number, email: email, userId: userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/AddContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("contactAddResult").innerHTML = "Contact has been added";
				loadContacts();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactAddResult").innerHTML = err.message;
	}
	
}

function deleteContact()
{
	let contactToDelete = document.getElementById("deleteText").value;
	document.getElementById("contactDeleteResult").innerHTML = "";

	let tmp = {contact: contactToDelete, userId: userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/DeleteContact.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("DELETE", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("contactDeleteResult").innerHTML = "Contact has been deleted successfully";
				loadContacts();
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactDeleteResult").innerHTML = err.message;
	}
	
}

function searchContact()
{
	let srch = document.getElementById("searchText").value;
	document.getElementById("contactSearchResult").innerHTML = "";
	
	let contactList = "";

	let tmp = {search:srch,userId:userId};
	let jsonPayload = JSON.stringify( tmp );

	let url = urlBase + '/SearchContacts.' + extension;
	
	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTF-8");
	try
	{
		xhr.onreadystatechange = function() 
		{
			if (this.readyState == 4 && this.status == 200) 
			{
				document.getElementById("contactSearchResult").innerHTML = "Contact(s) has been retrieved";
				let jsonObject = JSON.parse( xhr.responseText );
				
				for( let i=0; i<jsonObject.results.length; i++ )
				{
					contactList += jsonObject.results[i];
					if( i < jsonObject.results.length - 1 )
					{
						contactList += "<br />\r\n";
					}
				}
				
				document.getElementsByTagName("p")[0].innerHTML = contactList;
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err)
	{
		document.getElementById("contactSearchResult").innerHTML = err.message;
	}
	
}

function loadContacts() {
	let url = urlBase + '/GetContact.' + extension;

	let tmp = {userId: userId};
	let jsonPayload = JSON.stringify(tmp);

	let xhr = new XMLHttpRequest();
	xhr.open("POST", url, true);
	xhr.setRequestHeader("Content-type", "application/json; charset=UTG-8");

	try {
		xhr.onreadystatechange = function() {
			if(this.readyState == 4 && this.status == 200)
			{
				let response = JSON.parse(xhr.responseText);
				displayContacts(response.contacts);
			}
		};
		xhr.send(jsonPayload);
	}
	catch(err) {
		document.getElementById("contactDeleteResult").innerHTML = err.message;
	}
}

function displayContacts(contacts) {
    let contactList = document.getElementById("contactTableBody");
    contactList.innerHTML = "";

    contacts.forEach(contact => {
        let row = document.createElement("tr");

		let name = document.createElement("td");
		name.textContent = contact.name;
		row.appendChild(name);

		let number = document.createElement("td");
		number.textContent = contact.number;
		row.appendChild(number);

		let email = document.createElement("td");
		email.textContent = contact.email;
		row.appendChild(email);

		let action = document.createElement("td");
        let deleteButton = document.createElement("button");
        deleteButton.textContent = "Delete";
		deleteButton.classList.add("delete-button");

        deleteButton.onclick = function () {
            deleteContact(contact.id);
        };

        action.appendChild(deleteButton);
        row.appendChild(action);

		contactList.appendChild(row);
    });
}

function saveCookie() {
    let minutes = 20;
    let date = new Date();
    date.setTime(date.getTime() + (minutes * 60 * 1000));
    let expires = "; expires=" + date.toUTCString() + "; path=/";

    document.cookie = `userId=${userId}${expires}`;
    document.cookie = `firstName=${encodeURIComponent(firstName)}${expires}`;
    document.cookie = `lastName=${encodeURIComponent(lastName)}${expires}`;
}

function readCookie() {
    userId = -1;
    let cookies = document.cookie.split(";");

    for (let i = 0; i < cookies.length; i++) {
        let cookie = cookies[i].trim();
        let [key, value] = cookie.split("=");

        if (key === "userId") userId = parseInt(value);
        if (key === "firstName") firstName = decodeURIComponent(value);
        if (key === "lastName") lastName = decodeURIComponent(value);
    }

}

document.addEventListener("DOMContentLoaded", readCookie);

