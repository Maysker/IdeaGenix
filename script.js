function submitForm() {
    const formData = new FormData(document.getElementById('contentForm'));
    fetch('process.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // Clear previous results
        document.getElementById('trendsResult').innerHTML = '';
        document.getElementById('ideasResult').innerHTML = '';

        // Check if there are trend data
        if (data.trendsData) {
            // Process and display trend data
            document.getElementById('trendsResult').innerHTML = '<h2>Topic popularity in Google trends:</h2>';
            data.trendsData.interest_over_time.timeline_data.forEach(item => {
                const p = document.createElement('p');
                p.textContent = `${item.date}: ${item.values.map(value => value.extracted_value).join(', ')}`;
                document.getElementById('trendsResult').appendChild(p);
            });
        }

        // Check if there are generated content ideas
        if (data.contentIdeas) {
            // Process and display content ideas
            document.getElementById('ideasResult').innerHTML = '<h2>Content Ideas:</h2>';
            data.contentIdeas.generations.forEach(gen => {
                const p = document.createElement('p');
                p.textContent = gen.text;
                document.getElementById('ideasResult').appendChild(p);
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('trendsResult').innerHTML = 'Error fetching data.';
        document.getElementById('ideasResult').innerHTML = 'Error fetching data.';
    });
}
