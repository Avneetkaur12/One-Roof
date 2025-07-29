//Validations
function validateContactDetails(phone, email) {
    const phoneNum = /^[0-9]{10}$/;

    const emailID = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    let errorMessage = '';

    if (!phoneNum.test(phone)) {
        errorMessage += 'Invalid phone number. It must be a 10-digit number.\n';
    }

    if (!emailID.test(email)) {
        errorMessage += 'Invalid email address.\n';
    }

    if (errorMessage) {
        alert(errorMessage);
        return false;
    }

    return true;
}

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('contactForm');

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const formData = new FormData(form);

        const data = {};
        formData.forEach((value, key) => {
            data[key] = value;
        });

        const isEmptyField = Object.values(data).some(fieldValue => !fieldValue.trim());
        if (isEmptyField) {
            alert('All fields are required. Please fill in every field.');
            return;
        }

        const isValid = validateContactDetails(data.phone, data.email);

        if (!isValid) {
            return;
        }


        try {
            const response = await fetch('http://localhost:8000/contact', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: new URLSearchParams(data)
            });

            const result = await response.json();

            if (result.status == 'success') {
                console.log('Success:', result);
                alert('Contact saved successfully!');
                form.reset();
            } else {
                console.error('Error:', result);
                alert(`Error: ${result.message || 'An error occurred while saving the contact.'}`);
            }
        } catch (error) {
            console.error('Fetch error:', error);
            alert('A network error occurred. Please try again later.');
        }
    });
});

//Function to fetch all the contacts
async function getContacts() {
    try {
        const response = await fetch('http://localhost:8000/contact');
        if (!response.ok) throw new Error('Failed to fetch contacts');
        const data = await response.json();

        if (data.status === 'success') {
            populateTable(data.data);
        } else {
            console.error(data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

//Function to display All contacts
function populateTable(contacts) {
    const tableBody = document.querySelector('#contactsTable tbody');
    tableBody.innerHTML = '';

    contacts.forEach(contact => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><input type='text' readonly style='border: none;' value='${contact.id}' data-field='id'></td>
            <td><input type='text' readonly style='border: none;' value='${contact.name}' data-field='name'></td>
            <td><input type='number' readonly style='border: none;' value='${contact.phone}' data-field='phone'></td>
            <td><input type='email' readonly style='border: none;' value='${contact.email}' data-field='email'></td>
            <td><input type='text' readonly style='border: none;' value='${contact.address}' data-field='address'></td>
            <td><button onclick="toggleEdit(${contact.id}, this)">Edit</button></td>
            <td><button onclick="deleteContact(${contact.id})">Delete</button></td>
        `;
        tableBody.appendChild(row);
    });
}

//Function to change the text of button while editing the contacts
function toggleEdit(id, button) {
    const row = button.parentElement.parentElement;
    const inputs = row.querySelectorAll('input');
    const isEditing = button.textContent === 'Save';

    if (isEditing) {
        updateContact(id, inputs);
        button.textContent = 'Edit';
    } else {
        inputs.forEach(input => {
            if (input.getAttribute('data-field') !== 'id') {
                input.removeAttribute('readonly');
            }
        });

        button.textContent = 'Save';
    }

}

//Function to update contacts
async function updateContact(id, inputs) {
    const updatedData = {};
    inputs.forEach(input => {
        updatedData[input.getAttribute('data-field')] = input.value;
    });

    const isEmptyField = Object.values(updatedData).some(fieldValue => !fieldValue.trim());
    if (isEmptyField) {
        alert('All fields are required. Please fill in every field.');
        getContacts();
        return;
    }

    const isValid = validateContactDetails(updatedData.phone, updatedData.email);

    if (!isValid) {
        getContacts();
        return;
    }


    try {
        const response = await fetch(`http://localhost:8000/contact?id=${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams(updatedData)
        });

        const data = await response.json();
        if (data.status === 'success') {
            alert('Contact updated successfully');
            getContacts(); 
        } else {
            alert('Failed to update contact: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

//Function to delete contacts
async function deleteContact(id) {
    if (confirm('Are you sure you want to delete this contact?')) {
        try {
            const response = await fetch(`http://localhost:8000/contact?id=${id}`, {
                method: 'DELETE'
            });

            const data = await response.json();
            if (data.status === 'success') {
                alert('Contact deleted successfully');
                getContacts(); 
                document.getElementById('contactDetails').innerHTML = ''; 
            } else {
                alert('Failed to delete contact: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }
}
window.onload = getContacts;
