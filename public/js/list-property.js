function loadCountries() {
    const selectDrop = document.querySelector('#country');
    if (selectDrop) {
        fetch('https://restcountries.com/v3.1/all')
            .then(res => res.json())
            .then(data => {
                let options = '<option value="">Select Country</option>';
                data.forEach(country => {
                    options += `<option value="${country.name.common}">${country.name.common}</option>`;
                });
                selectDrop.innerHTML = options;
            })
            .catch(err => {
                console.log(err);
                selectDrop.innerHTML = '<option value="">Country list unavailable</option>';
            });
    }
}
document.addEventListener('DOMContentLoaded', loadCountries);
