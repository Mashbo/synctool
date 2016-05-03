# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|

    # Private box
    config.vm.box = "mashbo/web-and-db"

    config.vm.provision "docker" do |d|
        d.pull_images "ubuntu"
    end
end
