#Project: SolarFlow Data Visualization

This project aims to visualize solar power data using an interactive line chart. The goal is to represent solar power generation over the past 6 hours based on data points. Due to limitations in the server environment and the inability to install libraries or perform complex data manipulations server-side, this approach relies on using static, simulated data integrated into the frontend.
Technologies

    - HTML/CSS: Provides the basic structure and design of the webpage.
    - JavaScript: Handles dynamic interactions and data processing.
    - Chart.js: Enables the creation of the interactive line chart.

##How it Works

The project utilizes Chart.js, a popular JavaScript library, to visualize solar power data in an appealing line chart format. Since direct access to the data.txt file containing the solar power data is not feasible due to server constraints, the data is simulated or manually inputted. The JavaScript code then fetches this data and dynamically updates the chart accordingly, providing users with insights into solar power generation trends over time.

This project is inspired by the solarflow(control; bt-manager; statuspage) project of reinhard-brandstaedter, which can be found at https://github.com/reinhard-brandstaedter/solarflow-control/.
The webview can be found at solar.learnforschool.de
