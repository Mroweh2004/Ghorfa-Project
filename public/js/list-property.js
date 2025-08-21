function loadCountries() {
    const selectDrop = document.querySelector('#country');
    if (selectDrop) {
        fetch('https://countriesnow.space/api/v0.1/countries')
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select Country</option>';
                if (data && data.data && Array.isArray(data.data)) {
                    data.data.forEach(country => {
                        options += `<option value="${country.country}">${country.country}</option>`;
                    });
                }
                selectDrop.innerHTML = options;
                $('#country').select2({
                    placeholder: "Select Country",
                    allowClear: true,
                    width: "100%"
                });
            })
            .catch(err => {
                console.error(err);
                selectDrop.innerHTML = '<option value="">Country list unavailable</option>';
            });
    }
}

document.addEventListener('DOMContentLoaded', loadCountries);