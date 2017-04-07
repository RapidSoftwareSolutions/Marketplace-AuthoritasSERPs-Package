[![](https://scdn.rapidapi.com/RapidAPI_banner.png)](https://rapidapi.com/package/AuthoritasSERPs/functions?utm_source=RapidAPIGitHub_AuthoritasSERPsFunctions&utm_medium=button&utm_content=RapidAPI_GitHub)
# AuthoritasSERPs Package
Get keyword rankings, PPC data, and search engine rankings.
* Domain: [Authoritas](https://www.authoritas.com/)
* Credentials: apiKey, apiSalt, apiSecret

## How to get credentials: 
1. Upgrade your account, send email to support to get apiKeys
 
## AuthoritasSERPs.createSERPsJob
Creates a new job to be processed. Upon successfully accepting the request you will receive a job id (UUID) that can later be used to retrieve the SERPs data

| Field                | Type       | Description
|----------------------|------------|----------
| apiKey               | credentials| Api key
| apiSalt              | credentials| Api salt
| apiSecret            | credentials| Api secret
| searchEngine         | String     | The search engine to query. Examples: bing, google, yahoo, yandex, baidu
| phrase               | String     | The search term / keyword
| region               | String     | The region code to use
| language             | String     | The language code to use.
| town                 | String     | The town to use
| searchType           | String     | The type of search to perform: web site or country. Default: web
| maxResults           | Number     | The number of results to return. Any number up to 500. Default: 100
| strategy             | String     | The serps fetch strategy. standard - this is the default strategy, we will try to fetch SERPs with 10 results per page, so max_results: 100 will yield 10 pages. economic - we will try to fetch the first X pages with 10 results each then a last page with the remainder, so max_results: 100 will yield X+1 pages
| fullPagesCount       | Number     | Work only with strategy = Economic. Fetch the first full_pages_count pages with 10 results each then a last page with the remainder, so max_results: 100 will yield full_pages_count + 1 pages. Default: 3
| userAgent            | String     | The user agent to use. See below
| useCache             | Boolean    | Wether to use cached data, if available
| includeAllInUniversal| Boolean    | Wether to incude ads in universal section. Default false

## AuthoritasSERPs.getSERPsJob
Returns information about an existing job.

| Field    | Type       | Description
|----------|------------|----------
| apiKey   | credentials| Api key
| apiSalt  | credentials| Api salt
| apiSecret| credentials| Api secret
| jobId    | String     | Job ID (UUID)

