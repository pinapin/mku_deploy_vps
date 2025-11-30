// Function to convert month names to Indonesian
function convertMonthToIndonesian(date) {
    const monthsIndo = {
        January: "Januari",
        February: "Februari",
        March: "Maret",
        April: "April",
        May: "Mei",
        June: "Juni",
        July: "Juli",
        August: "Agustus",
        September: "September",
        October: "Oktober",
        November: "November",
        December: "Desember",
    };

    let formattedDate = moment(date).format("DD MMMM YYYY");

    // Replace English month names with Indonesian ones
    Object.keys(monthsIndo).forEach((month) => {
        formattedDate = formattedDate.replace(month, monthsIndo[month]);
    });

    return formattedDate;
}
