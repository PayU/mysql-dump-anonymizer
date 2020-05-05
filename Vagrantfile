# -*- mode: ruby -*-
# vi: set ft=ruby :

VAGRANTFILE_API_VERSION = '2'

Vagrant.configure(VAGRANTFILE_API_VERSION) do |config|

    config.vm.box = 'ubuntu/bionic64'
    config.vm.box_check_update = true
    config.vm.provider :virtualbox do |vb|
        vb.name = 'anonymizer'

        vb.memory = 1024
        vb.cpus = 2

        vb.customize ['modifyvm', :id, '--ioapic', 'on']
        vb.customize ['modifyvm', :id, '--cpuexecutioncap', '100']
    end

    config.vm.provision 'docker'

end
