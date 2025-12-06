// world.js

document.addEventListener("DOMContentLoaded", () => {
  const countryInput = document.getElementById("country");
  const countryBtn = document.getElementById("lookup-country");
  const citiesBtn = document.getElementById("lookup-cities");
  const resultDiv = document.getElementById("result");

  function showMessage(message, isError = false) {
    const cls = isError ? "message error" : "message";
    resultDiv.innerHTML = `<p class="${cls}">${message}</p>`;
  }

  async function fetchResults(lookupType = "country") {
    const country = countryInput.value.trim();

    if (!country) {
      showMessage("Please enter a country name first.");
      return;
    }

    try {
      const params = new URLSearchParams({ country });
      if (lookupType === "cities") {
        params.append("lookup", "cities");
      }

      const response = await fetch(`world.php?${params.toString()}`);

      if (!response.ok) {
        throw new Error("Network response was not ok");
      }

      const html = await response.text();
      resultDiv.innerHTML = html;
    } catch (err) {
      console.error(err);
      showMessage(
        "Something went wrong while fetching data. Please try again.",
        true
      );
    }
  }

  countryBtn.addEventListener("click", () => fetchResults("country"));
  citiesBtn.addEventListener("click", () => fetchResults("cities"));

  // country lookup
  countryInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
      fetchResults("country");
    }
  });
});
