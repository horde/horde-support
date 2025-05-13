#!/bin/bash
PAT="${GITHUB_TOKEN:-NONE}"
declare -l clean_hostname=$(hostname -s)
clean_hostname="${clean_hostname//[^[:alnum:]]/}"

# if no pat is set
if [ "$PAT" == "NONE" ]; then
    echo "❌ No GITHUB_TOKEN variable set. You need a github token."
    echo "If you have no valid token or it expired, follow this link"
    echo "https://github.com/settings/tokens/new?description=horde-developer-$clean_hostname&scopes=repo,read:packages,read:org,workflow"
    echo "Once created, save the token and inject it into your environment"
    echo "For example, in bash you can enter:"
    echo "echo 'export GITHUB_TOKEN=your_token' >> ~/.bashrc"
    exit 1
else
    echo "✅ Found GITHUB_TOKEN variable"
fi

## Check if github client is installed 
if ! command -v gh &> /dev/null
then
    echo "❌ gh client tool could not be found. Please install the GitHub CLI."
    echo "https://cli.github.com/"
    exit 1
else
    echo "✅ gh is installed"
fi