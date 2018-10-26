![Gemguide logo](https://app.gemguide.com/static/images/logo.png)


# Gemguide Pricing Data API

This documentation is intended for developers learning how to use the Gemguide Pricing API. 

All of the steps required will be outlined below, but if you would like to see a working example to supplement your understanding, please see the following [repository](https://github.com/lioninteractive/gemguide-api-docs).


## Setup

If you are reading this documentation then you have presumably already have your API key and have been added as an API User authorized to make requests to the Gemguide Pricing API. This key will be required for all requests.


In addition to receiving an API key, you will have had to provid two URL’s to Gemguide:

1. **Success callback:** This is the page you direct users to if they have been successfully authenticated upon logging in to your app. *This callback will be appended with the user's unique identifier as a query argument, which will be required for all requests.*
	
2. **Failure callback:** This is the page you direct users to if anything goes wrong during the authentication process.


## Authentication

The Gemguide Pricing API uses OAuth1 to authenticate users registered with [Gemworld International](https://gemguide.com).

In order to authenticate users and receive their unique identifier required for all requests, you will need to create a log-in button that sends them to the following link where `client_key` is the API key provided to you by Gemguide, as detailed in the **Setup** section above:

`https://app.gemguide.com/api_authorize?client_key={your API key}`

If they are successfully authenticated, then they will be eventually routed back to your **Success callback** as detailed in the **Setup** section above. The success URL could look like the following, where the `user` query arg is appended by Gemguide. This identifier must be retrieved and stored by your app, as it is required in order to make future requests:

`https://app.yourapp.com/success_callback?user={unique identifier}`

**This unique identifier will change every time the user logs in, so it is important that you update this value every time that they do.**

If anything goes wrong during the authentication process, then the user will be redirect to the **Failure callback** as detailed in the **Setup** section above.


## Request Structure

The basic structure for requests is:

`https://app.gemguide.com/prices-api/{route}?arg1=foo&arg2=bar`

Where `{route}` is either `gem` or `diamond` depending on what pricing data you are after.


For the Colored Gemstone route, this could look like:

`https://app.gemguide.com/prices-api/gem?&name=Almandine%20Garnet&weight=1`

For the Diamond route, this could look like:

`https://app.gemguide.com/prices-api/diamond?name=Emerald&weight=1&color=G&clarity=IF/FL`

**Authorization:**

In order for any of the above requests to work, you will need to provide two parameters as headers:

1. `api_key`: This is the API key that Gemguide  provided to you.
2. `user`: This is the unique identifier for the Gemguide user logged in to your app. You receive this ID via the success callback detailed in the **Setup** and **Authentication** sections above.


<u>Error Handling</u>

Error responses use the following format:

	{
		code: 'error_code',
		message: 'This is a message describing the error in human friendly language'
	}
	

Invalid requests may receive one of the following error responses:

- `user_unauthenticated`: This means your API key is invalid or no longer registered.
- `user_client_unauthenticated`: This means the unique ID of the user (as detailed in the **Authentication** section above) is invalid or has since changed. 
- `user_client_expired`: This means that it has been 30 days since the unique ID of the user has been updated. **A user is required to log in and be re-authenticated at least once every 30 days in order to prevent this error from happening.**


## Routes

###<u>Colored Gemstones</u>

The structure for the Colored Gemstone route is as follows: 

`https://app.gemguide.com/prices-api/gem?&name={name}&weight={weight}`

There are two required arguments:

1. `name`: This argument is the name of the gem. It is **case sensitive** and must correspond **precisely** to the colored gemstone's name in the [Gemguide App](https://app.gemguide.com). Space characters should be escaped as `%20` i.e. `Almandine%20Garnet`
2. `weight`: This is the weight of the gemstone in carats.

The response will be an object of arrays like so:

```
{
    "1": [
        2
    ],
    "2": [
        2
    ],
    "3": [
        3
    ],
    "4": [
        4
    ],
    "5": [
        5
    ],
    "6": [
        7
    ],
    "7": [
        9
    ],
    "8": [
        10
    ],
    "9": [
        12
    ],
    "10": [
        15
    ]
}
```

The values 1 through 10 represent increasing levels of clarity, which affect the prices. The reason the price values are given as arrays is because sometimes there will be one value (set recommended price) and sometimes there will be a range of values (recommended price range). **The developer can assume that these arrays will always contain either zero, one, or two values, depending respectively upon whether it's valid configuration, a range, or a set price.**


<u>Error Handling</u>

Error responses use the following format:

	{
		code: 'error_code',
		message: 'This is a message describing the error in human friendly language'
	}

Invalid requests may receive one of the following error responses:

- `no_shape_name_provided`: The request was missing a value for `name`
- `no_weight_provided`: The request was missing a value for `weight`
- `invalid_gem`: The value for `name` does not match any gemstone names in the Gemguide database.
- `invalid_weight_nan`: The `weight` specified is not a number
- `invalid_weight`: The `weight` specified falls outside the range of weights for the requested gem. The gem's valid weight range will be specified in the `message` property in the error response.
- `server_error`: There was an unexpected server issue.


###<u>Diamonds</u>

The structure for the Colored Gemstone route is as follows: 

`https://app.gemguide.com/prices-api/diamond?name={name}&weight={weight}&color={color}&clarity={clarity}`

There are four required arguments:

1. `name`: This is the name of the diamond shape. It is **case sensitive** and must correspond **precisely** to the diamond shape's name in the [Gemguide App](https://app.gemguide.com). Space characters should be escaped as `%20` i.e. `Old%20European`
2. `weight`: This is the weight of the diamond in carats.
3. `color`: This is the **case sensitive** color value of the diamond. Colors values are specified as upper-case letters from D-M (i.e, `D`, `E`, `F`, `G`, `H`, `I`, `J`, `K`, `L`, `M`)
4. `clarity`: This is the **case sensitive** clarity value of the diamond. Clarity values must be one of the following:  `IF/FL`, `VVS1`, `VVS2`, `VS1`, `VS2`, `SI1`, `SI2`, `I1`, `I2`, `I3`

The response will be an array of three arrays, each with three values, like so:

```
[
    [
        "-",
        6930,
        6230
    ],
    [
        "-",
        5530,
        5050
    ],
    [
        "-",
        4700,
        4200
    ]
]
```

These arrays represent the price of the diamond configuration you specified in the query arguments, as well as their adjacent values. The exact price that is derived based on the query arguments can be found at `array[1][1]` (the center value). The value at `array[1][0]` represents the same configuration except one clarity level higher, likewise the value at `array[1][2]` represents the same configuration at one clarity level lower. The values in `array[0]` and `array[2]` represent the same clarities found in `array[1]` except they are based on the next lower (`array[0]`) or next higher (`array[2]`) color value.

So another way to visualize a given response could be like so (not precisely based on the above example):

```
[
	D => [
		IF/FL,
		VVS1,
		VVS2
	],
	E => [
		IF/FL,
		VVS1,
		VVS2
	],
	F => [
		IF/FL,
		VVS1,
		VVS2
	]
]
```

There will be instances, such as in the first diamond response example, where there are no valid values for a specified configuration. For example, if you select `IF/FL` as your clarity level, there is no higher clarity level to reference. In these cases, instead of a price, the value will be filled in with a hyphen (`-`). This can also happen with colors, as you cannot go higher than `D` or lower than `M`.

To further elaborate this, if you set a color to be `D` (the highest color value) and clarity to be `I3` (the lowest clarity value) you would get a response that looks like this:

```
[
    [
        "-",
        "-",
        "-"
    ],
    [
        1700,
        1020,
        "-"
    ],
    [
        1615,
        935,
        "-"
    ]
]
```

So in the above case, you can only reference lower color levels and higher clarity levels.


<u>Error Handling</u>

Error responses use the following format:

	{
		code: 'error_code',
		message: 'This is a message describing the error in human friendly language'
	}

Invalid requests may receive one of the following error responses:

- `no_shape_name_provided`: The request was missing a value for `name`
- `no_color_provided`: The request was missing a value for `color`
- `no_clarity_provided`: The request was missing a value for `clarity`
- `no_weight_provided`: The request was missing a value for `weight`
- `invalid_shape`: The `name` specified did not match a diamond shape name.
- `invalid_weight_nan`: The `weight` specified is not a number
- `invalid_weight`: The `weight` specified falls outside the range of weights for the requested diamond. The diamond shape's valid weight range will be specified in the `message` property in the error response.
- `invalid_color`: The `color` value did not match a valid color. Valid colors are specified at the top of this section (**Routes#Diamonds**)
- `invalid_clarity`: The `color` value did not match a valid clarity. Valid clarities are specified at the top of this section (**Routes#Diamonds**)
- `server_error`: There was an unexpected server issue.

































