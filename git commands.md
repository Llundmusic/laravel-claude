git add . (adds all files, can also add specific files..)
git commit -m "comment" (commit -a for all new files as well?)
git push -u -f origin main (or other branch name)

git pull (to sync other machine)

new branch:
git checkout -b branchname

merging:
git checkout main
git merge branchname (to be merged)


delete a local branch:
git branch -d branch_name

force delete a local branch:
git branch -D branch_name

delete a remote branch:
git push origin --delete branch_name



other cleanup not git..

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
composer dump-autoload


REMOVE LOCAL BRANCHES:
Ctrl+Shift+P -> Git: Delete Branch...